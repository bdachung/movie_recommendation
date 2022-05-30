import numpy as np
import pandas as pd
from scipy.sparse import coo_matrix

u_cols =  ['user_id', 'age', 'sex', 'occupation', 'zip_code']

users = pd.read_csv('data/ml-100k/u.user', sep='|', names=u_cols, encoding='latin-1')

r_cols = ['user_id', 'movie_id', 'rating', 'unix_timestamp']

ratings = pd.read_csv('data/ml-100k/ua.base', sep='\t', names=r_cols, encoding='latin-1')

i_cols = ['movie_id', 'movie_title' ,'release_date','video_release_date', 'IMDb_URL', 'unknown', 'Action', 'Adventure',
 'Animation', 'Children_s', 'Comedy', 'Crime', 'Documentary', 'Drama', 'Fantasy',
 'Film_Noir', 'Horror', 'Musical', 'Mystery', 'Romance', 'Sci_Fi', 'Thriller', 'War', 'Western']

items = pd.read_csv('data/ml-100k/u.item', sep='|', names=i_cols, encoding='latin-1')

N_USERS = users.shape[0]
N_RATINGS = ratings.shape[0]
N_ITEMS = items.shape[0]

average_ratings = pd.DataFrame()
average_ratings['movie_id'] = items['movie_id']

watched_matrix = coo_matrix((ratings['rating'],(ratings['user_id']-1,ratings['movie_id']-1)),(N_USERS, N_ITEMS))

def calculate_average_ratings():
    average_ratings['rating'] = ratings[['movie_id','rating']].groupby(by=["movie_id"], axis=0, as_index=False).mean()['rating']
    average_ratings.fillna(0, inplace=True)
    average_ratings.sort_values(by="rating", inplace=True, ascending=False)
    
calculate_average_ratings()

def popularity_rec(userid, k):
    already_watched = watched_matrix.toarray()[userid-1]
    result = []
    count = 0
    for movie in average_ratings['movie_id']:
        if already_watched[movie-1] != 0:
            result.append(movie)
            count = count + 1
        if(count == k):
            break
    return 

print(popularity_rec(1,10))