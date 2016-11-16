#!/Users/Kavinya/.pyenv/versions/3.5.2/bin/python

import psycopg2
import os
import sys
import json

script_dir = os.path.dirname(os.path.realpath(__file__))
config = json.load(open(script_dir + '/../config.json'))

dbname = config['dbname']
password = config['password']
user = config['user']
csv_dir = script_dir + '/../data/csv'

def getOpenConnection():
    """
    :param user: Postgresql user name
    :param password: Postgresql password
    :param dbname: Postgresql DB name - webmd_dataset for this project
    :return: open connection to query the database
    """
    """
    :param user:
    :param password:
    :param dbname:
    :return:
    """
    return psycopg2.connect("dbname='" + dbname + "' user='" + user + "' host='localhost' password='" + password + "'")


def createDB():
    """
    :param dbname: Name of the database to be created
    """
    con = getOpenConnection()
    con.set_isolation_level(psycopg2.extensions.ISOLATION_LEVEL_AUTOCOMMIT)
    cur = con.cursor()

    # Check if an existing database with the same name exists
    cur.execute('SELECT COUNT(*) FROM pg_catalog.pg_database WHERE datname=\'%s\'' % (dbname,))
    count = cur.fetchone()[0]

    if count == 0:
        cur.execute('CREATE DATABASE %s' % (dbname,))  # Create the database
    else:
        print ('A database named {0} already exists'.format(dbname))

    # Clean up
    cur.close()
    con.commit()
    con.close()

def loadAnswer(openconnection):
    """
    :param openconnection:
    creates table and loads answer data
    """
    cur = openconnection.cursor()
    cur.execute("DROP TABLE IF EXISTS answer")
    cur.execute(
        "CREATE TABLE answer (answerId Integer, questionId Integer, answerQuestionURL Text, answerMemberId Text, \
                answerContent Text,answerPostDate Text, answerHelpfulNum Text, answerVoteNum Text,  \
                PRIMARY KEY(answerId))")
    cur.execute("COPY answer FROM '" + csv_dir + "/webmd-answer.csv' DELIMITER \
                ',' CSV HEADER;")
    cur.close()
    openconnection.commit()

def loadQuestion(openconnection):
    """
    :param openconnection:
    creates table and loads question data
    """
    cur = openconnection.cursor()
    cur.execute("DROP TABLE IF EXISTS question")
    cur.execute(
        "CREATE TABLE question (questionId Integer, questionTopicId Text, questionTitle Text, questionMemberId Integer, \
                questionContent Text, questionPostDate Text, questionURL Text, PRIMARY KEY(questionId))")
    cur.execute("COPY question FROM '" + csv_dir + "/webmd-question.csv' DELIMITER \
                ',' CSV HEADER;")
    cur.close()
    openconnection.commit()

def loadRelatedTopic(openconnection):
    """
    :param openconnection:
    creates table and loads related-topic data
    """
    cur = openconnection.cursor()
    cur.execute("DROP TABLE IF EXISTS related_topic")
    cur.execute("CREATE TABLE related_topic (questionId Integer, topicId Text)")
    cur.execute("COPY related_topic FROM '" + csv_dir + "/webmd-related_topic.csv' \
                DELIMITER ',' CSV HEADER;")
    cur.close()
    openconnection.commit()

def loadTopics(openconnection):
    """
    :param openconnection:
    create topics and loads topics data
    """
    cur = openconnection.cursor()
    cur.execute("DROP TABLE IF EXISTS topics")
    cur.execute("CREATE TABLE topics (topicId Text, topicName Text, PRIMARY KEY(topicId))")
    cur.execute("COPY topics FROM '" + csv_dir + "/webmd-topics.csv' DELIMITER \
                ',' CSV HEADER;")
    cur.close()
    openconnection.commit()

# Create DB if not exists
createDB()
# Get connection and load all the clean data
con = getOpenConnection()
loadAnswer(con)
loadQuestion(con)
loadTopics(con)
loadRelatedTopic(con)
#close the connection
con.close()