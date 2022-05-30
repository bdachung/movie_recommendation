import pickle
import pandas as pd 
import numpy as np
from sklearn.metrics.pairwise import cosine_similarity
from scipy import sparse 
from module import CF, similarity
from fastapi import FastAPI, Form
import pandas as pd
from starlette.responses import HTMLResponse
import uvicorn
import mysql.connector
from scipy.sparse import coo_matrix, vstack, hstack
from pydantic import BaseModel
from fastapi.staticfiles import StaticFiles
from fastapi.middleware.cors import CORSMiddleware
from sklearn.feature_extraction.text import TfidfTransformer
from sklearn.cluster import KMeans

class User(BaseModel):
    user_id: int

class INTER(BaseModel):
    num: int

class Movie(BaseModel):
    movie_id: int

class Rating(BaseModel):
    user_id: int
    movie_id: int
    rating: int

K_POPULARITY = 10
K_COLLAB = 20

model = None

with open('./checkpoint/model.pkl','rb') as f:
    model = pickle.load(f)

app = FastAPI()

origins = [
    "http://localhost",
    "http://localhost:8080"
]

app.add_middleware(
    CORSMiddleware,
    allow_origins=origins,
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

app.mount("/static", StaticFiles(directory="static"), name="static")

def connection(host="localhost",user="root",password="",database="recommendation"):
    return mysql.connector.connect(host=host,user=user,password=password,database=database)

mydb = connection()
mycursor = mydb.cursor()

# n_users = 0
# n_items = 0
ratings = pd.DataFrame()
average_ratings = pd.DataFrame()
items = pd.DataFrame()
item_cluster = pd.DataFrame()
# watched_matrix = None

def calculate_average_ratings():
    average_ratings['rating'] = ratings[['movie_id','rating']].groupby(by=["movie_id"], axis=0, as_index=False).apply(lambda x : np.mean(x, axis=0)*np.log(len(x) + 1) if len(x) else 0)['rating']
    average_ratings.fillna(0, inplace=True)
    average_ratings.sort_values(by="rating", inplace=True, ascending=False)

def updateData():
    mydb = connection()
    mycursor = mydb.cursor()
    # global n_users,n_items,ratings,average_ratings,watched_matrix
    global ratings, average_ratings, items
    # n_users_old = n_users
    # n_items_old = n_items
    ratings_old = ratings.copy() 
    average_ratings_old = average_ratings.copy() 
    items_old = items.copy()
    # watched_matrix_old = watched_matrix.copy() if watched_matrix != None else None
    # try:
        # query = "SELECT count(*) FROM users;"
        # mycursor.execute(query)
        # n_users = mycursor.fetchall()[-1][-1]

        # query = "SELECT count(*) FROM items;"
        # mycursor.execute(query)
        # n_items = mycursor.fetchall()[-1][-1]

    query = "SELECT * FROM ratings;"
    mycursor.execute(query)
    #user_id, movie_id, rating
    ratings = mycursor.fetchall()
    ratings = pd.DataFrame(ratings, columns=['user_id', 'movie_id', 'rating'])
    ratings['user_id'] -= 1
    ratings['movie_id'] -= 1

    query = "SELECT movie_id FROM items;"
    mycursor.execute(query)
    average_ratings = pd.DataFrame()
    average_ratings['movie_id'] = [movie[0] for movie in mycursor.fetchall()]
    print("line 110: pass movie_id of average_ratings")

    query = "SELECT * FROM item_type ;"
    mycursor.execute(query)
    items = mycursor.fetchall()
    items = pd.DataFrame(items, columns=['movie_id', 'movie_type'])
    items['movie_type'] = items['movie_type'].map({'Action':0, 'Adventure':1, 'Animation':2, 'Children_s':3, 'Comedy':4, 'Crime':5, 'Documentary':6, 'Drama':7, 'Fantasy':8, 'Film_Noir':9, 'Horror':10, 'Musical':11, 'Mystery':12, 'Romance':13, 'Sci_Fi':14, 'Thriller':15, 'War':16, 'Western':17}).astype(int)
    items['movie_id'] -= 1
    items = coo_matrix((np.ones((len(items),)), (items['movie_id'],items['movie_type'])),(int(np.max(items['movie_id']))+1,18))

    KNN()
    print("line 121: pass knn")        
    # calculate_average_ratings()
    print("line 123: pass popularity")
    
    mycursor.close()
    mydb.close()
    # watched_matrix = coo_matrix((ratings['rating'],(ratings['user_id'],ratings['movie_id'])),(n_users, n_items))

    return "Update data for AI server successful"
    # except:
    #     # n_users = n_users_old
    #     # n_items = n_items_old
    #     ratings = ratings_old
    #     average_ratings = average_ratings_old
    #     items = items_old
    #     # watched_matrix = watched_matrix_old
    #     return "Failed to update data for AI server"

def save_object(obj, filename):
    with open(filename, 'wb') as outp:
        pickle.dump(obj, outp, pickle.HIGHEST_PROTOCOL)

def retrainModel(k_users):
    global model
    try:
        updateData()
        newModel = CF(ratings.values,k_users)
        newModel.fit()
        save_object(newModel,'./checkpoint/model.pkl')
        model = newModel
        return "Retrain model successfuly"
    except:
        return "Fail to retrain model" 

def KNN():
    global average_ratings
    transformer = TfidfTransformer(smooth_idf=True, norm ='l2')
    x = np.array(items.toarray())
    lst = (average_ratings['movie_id'] - 1).values.tolist()
    tfidf = transformer.fit_transform(x[lst]).toarray()
    for k in [25,30,40,50,60,70]:
        kmeans = KMeans(n_clusters=k, random_state=0).fit(tfidf)
        average_ratings['cluster'] = kmeans.labels_
        loss = []
        for i in range(len(average_ratings)):
            inner = 0
            outer = []           
            for j in range(k):
                if(j == average_ratings.loc[i,'cluster']):
                    indices1 = np.where(average_ratings['cluster'] == j)[0]
                    rows1 = tfidf[indices1,:]
                    dist1 = tfidf[i,:] - rows1
                    dist1 = np.square(dist1)
                    l = np.sum(dist1,axis=-1)
                    inner = np.sum(np.sqrt(l)) / (len(l) - 1)
                else:
                    indices1 = np.where(average_ratings['cluster'] == j)[0]
                    rows1 = tfidf[indices1,:]
                    dist1 = tfidf[i,:] - rows1
                    dist1 = np.square(dist1)
                    l = np.sum(dist1,axis=-1)
                    outer.append(np.mean(l))
            outer = np.amin(outer,axis=-1)
            if inner < outer:
                loss.append(1 - inner / outer)
            else:
                loss.append(outer / inner - 1) 
        print(k,"clusters loss =",np.mean(loss))
        


updateData()

@app.post('/retraining')
def retraining(num: INTER):
    updateData()
    return retrainModel(num.num)

@app.get('/popularity', response_class=HTMLResponse)
def take_userid():
    return '''
        <form method="post">
            <h2>Get popular movies</h2>
            <input name="user_id" type="number"/>
            <input type="submit" />
        </form>
        <form method="post" action='./retraining'>
            <h2>Retrain model with k nearest users</h2>
            <input name="retrain" type="number"/ placeholder="Enter k nearest users">
            <input type="submit" />
        </form>
        <form method="post" action='./uu_rec'>
            <h2>Get movies of k similar users</h2>
            <input name="user_id" type="number"/>
            <input type="submit" />
        </form>
        <form method="post" action='./updateKPOP'>
            <h2>Enter the number k popular movies want to show</h2>
            <input name="num" type="number"/>
            <input type="submit" />
        </form>
        <form method="post" action='./updateKCOLLAB'>
            <h2>Enter the number k similar movies want to show</h2>
            <input name="num" type="number"/>
            <input type="submit" />
        </form>
        <input type="button" id="helloworld" onclick="sendToAI(document.forms[0].user_id.value)"/>
        <script src="/static/script.js"> 

        </script>'''

@app.post('/updateKPOP')
def popularity_rec(num: INTER):
    global K_POPULARITY
    try:
        K_POPULARITY = num.num
        return "Update K_POPULARITY successfully"
    except:
        return "Failed to update K_POPULARITY"

@app.post('/updateKCOLLAB')
def popularity_rec(num: INTER):
    global K_COLLAB
    try:
        K_COLLAB = num.num
        return "Update K_COLLAB successfully"
    except:
        return "Failed to update K_COLLAB"

@app.post('/popularity')
def popularity_rec(user_id: User):
    mydb = connection()
    mycursor = mydb.cursor()
    result = []
    count = 0
    query = "SELECT movie_id FROM ratings WHERE user_id = " + str(user_id.user_id) + ';'
    mycursor.execute(query)
    already_watched = [movie[0] for movie in mycursor.fetchall()]
    for movie in average_ratings['movie_id']:
        if (movie+1) not in already_watched:
            result.append(movie + 1)
            count = count + 1
        if(count == K_POPULARITY):
            break
    # return result
    query = "SELECT movie_id, movie_title FROM items WHERE "
    for i in range(count):
        if i==0:
            query += "movie_id = " + str(result[i])
        else:
            query += " OR movie_id = " + str(result[i])
    query += " ;"
    mycursor.execute(query)
    temp = mycursor.fetchall()
    mycursor.close()
    mydb.close()
    return ",".join(['"'+str(movie[0])+'"' + ":" + '"'+movie[1]+'"' for movie in temp])




    # already_watched = watched_matrix.toarray()[user_id-1]
    # for movie in average_ratings['movie_id']:
    #     if already_watched[movie-1] == 0:
    #         result.append(movie)
    #         count = count + 1
    #     if(count == K_POPULARITY):
    #         break
    # return result

@app.post('/uu_rec')
def uu_rec(user_id: User):
    mydb = connection()
    mycursor = mydb.cursor()
    result = model.recommend(int(user_id.user_id) - 1, K_COLLAB)
    print("rec by uu: ", result)
    if(len(result) == 0):
        return ""
    query = "SELECT movie_id, movie_title FROM items WHERE "
    for i in range(len(result)):
        if i==0:
            query += "movie_id = " + str(result[i])
        else:
            query += " OR movie_id = " + str(result[i])
    query += " ;"
    mycursor.execute(query)
    temp = mycursor.fetchall()
    mycursor.close()
    mydb.close()
    return ",".join(['"'+str(movie[0])+'"' + ":" + '"'+movie[1]+'"' for movie in temp])

@app.post('/knn_rec')
def knn_rec(movie: Movie):
    mydb = connection()
    mycursor = mydb.cursor()
    global average_ratings
    if(movie.movie_id in average_ratings['movie_id'].values):
        print("line 288: knn rec")
        indices = np.where(average_ratings['cluster'] == average_ratings.loc[average_ratings['movie_id']==movie.movie_id,'cluster'].values[0])
        movies = average_ratings.loc[indices[0],'movie_id']
        query = "SELECT movie_id, movie_title FROM items WHERE "
        x = movies.to_numpy()
        x = np.random.choice(x, size=10, replace=False)
        for i in range(len(x)):
            if i==0:
                query += "movie_id = " + str(x[i])
            else:
                query += " OR movie_id = " + str(x[i])
        query += " ;"
        mycursor.execute(query)
        temp = mycursor.fetchall()
    else:
        temp = []
    
    mycursor.close()
    mydb.close()
    return ",".join(['"'+str(movie[0])+'"' + ":" + '"'+movie[1]+'"' for movie in temp])





@app.post('/rate')
def rate(rating: Rating):
    if(rating.rating == 0):
        mydb = connection()
        mycursor = mydb.cursor()
        query = "SELECT rating FROM ratings WHERE user_id = " + str(rating.user_id) + " AND movie_id = " + str(rating.movie_id) + " ;"
        mycursor.execute(query)
        row = mycursor.fetchall()
        mycursor.close()
        mydb.close()
        if(len(row)):
            print("line 301")
            return row[-1][-1]
        else: 
            return 0
    else:
        mydb = connection()
        mycursor = mydb.cursor()
        query = "SELECT rating FROM ratings WHERE user_id = " + str(rating.user_id) + " AND movie_id = " + str(rating.movie_id) + " ;"
        mycursor.execute(query)
        row = mycursor.fetchall()
        mycursor.close()
        mydb.close()
        if(len(row)):
            try:
                mydb = connection()
                mycursor = mydb.cursor()
                query = "UPDATE ratings SET rating = " + str(rating.rating) + " WHERE user_id = " + str(rating.user_id) + " AND movie_id = " + str(rating.movie_id) + " ;"
                mycursor.execute(query)
                mydb.commit()
                mycursor.close()
                mydb.close()
                return "Rerate movie successfully. Hope you enjoy forever <3"
            except:
                return "Failed to rerate movie. You can try it again. Love you"
        else: 
            try:
                mydb = connection()
                mycursor = mydb.cursor()
                query = "INSERT INTO ratings(user_id,movie_id,rating) VALUES (%s, %s, %s)"
                val = (rating.user_id, rating.movie_id, rating.rating)
                mycursor.execute(query, val)
                mydb.commit()
                mycursor.close()
                mydb.close()
                return "Rate successfully. Thank you so much. I love you 3000"
            except:
                return "Failed to rate. Please rate again <3 to make your experience better"



if __name__ == '__main__':
    uvicorn.run(app, host='127.0.0.1', port=8000, debug=True)
