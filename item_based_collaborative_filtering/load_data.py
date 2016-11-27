#!/Users/Kavinya/.pyenv/versions/3.5.2/bin/python

import psycopg2
import pandas as pd
from scipy.spatial.distance import cosine
import csv

def getOpenConnection(user='Kavinya', password='', dbname='webmd_dataset'):
    """
    :param user: Postgresql username
    :param password: Postgresql password
    :param dbname: Database used - webmd_dataset for this project
    :return:
    """
    return psycopg2.connect("dbname='" + dbname + "' user='" + user + "' host='localhost' password='" + password + "'")

def getData(tableName, fieldName, openconnection):
    cur = openconnection.cursor()
    query = "select "+fieldName+" from "+tableName
    cur.execute(query)
    fieldList = cur.fetchall()
    field_ids = []
    for field in fieldList:
        if tableName == "related_topic":
            field_ids.append([field[0],field[1]])
        else:
            field_ids.append(field[0])
    cur.close()
    openconnection.commit()
    return field_ids

def generateMatrix(openconnection):
    question_ids = getData("question","questionid", con)
    topic_ids = getData("topics","topicid", con)
    related_topics = getData("related_topic","questionid, topicid", con)

    question_topic_matrix = []
    topics_index = {}

    # Heading
    headings = ['questionid']
    for i in range(len(topic_ids)):
        headings.append(topic_ids[i])
        topics_index[topic_ids[i]] = i

    question_topic_matrix.append(headings)

    # Initialize each value
    questions_output = {}
    for question in question_ids:
        questions_output[question] = []
        for topic in topic_ids:
            questions_output[question].append(str(0))

    # Update relation values
    for row in related_topics:
        questions_output[row[0]][topics_index[row[1]]] = str(1)

    # List of List
    for question in question_ids:
        questionlist = [question]+questions_output[question]
        question_topic_matrix.append(questionlist)

    df = pd.DataFrame(question_topic_matrix)
    df.to_csv('test.csv', index=False, header=False)

def generateSimilarity():
    data = pd.read_csv('question_topic_matrix.csv')
    data_germany = data.drop('questionid', 1)
    data_ibs = pd.DataFrame(index=data_germany.columns, columns=data_germany.columns)
    # Lets fill in those empty spaces with cosine similarities
    # Loop through the columns
    for i in range(0, len(data_ibs.columns)):
        print ("I is : "+str(i))
        # Loop through the columns for each column
        for j in range(0, len(data_ibs.columns)):
            # Fill in placeholder with cosine similarities
            data_ibs.ix[i, j] = 1 - cosine(data_germany.ix[:, i], data_germany.ix[:, j])

    df = pd.DataFrame(data_ibs)
    df.to_csv('data_ibs.csv', index=False, header=False)

# --- End Item Based Recommendations --- #

def loadData():
    with open("question_topic_matrix.csv", 'r') as data_file:
        reader = csv.reader(data_file, delimiter='\t')
        for row in reader:
            topics = row[0].split(',')
            break
    topics[0] = 'topicid'

con = getOpenConnection()
loadData()