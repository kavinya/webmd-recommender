#!/Users/Kavinya/.pyenv/versions/3.5.2/bin/python

import psycopg2
import os
import sys
import json
from psycopg2.extras import RealDictCursor

script_dir = os.path.dirname(os.path.realpath(__file__))
config = json.load(open(script_dir + '/../config.json'))

dbname = config['dbname']
password = config['password']
user = config['user']

def getOpenConnection():
    """
    :param user: Postgresql username
    :param password: Postgresql password
    :param dbname: Database used - webmd_dataset for this project
    :return:
    """
    return psycopg2.connect("dbname='" + dbname + "' user='" + user + "' host='localhost' password='" + password + "'")

def filterTopics(topicList,openconnection):
    """
    :param topicList: List of strings corresponding to substring of desired topics
    :param openconnection:
    :return: JSON object of {topicid, topicname} ; where each topicid has substring of elements in topicList
    """
    cur = openconnection.cursor(cursor_factory=RealDictCursor)
    query = "select * from topics"
    for topic in topicList:
        if query.find("where") > 0 :
            query = query + " or topicId like '%%%s%%'" %(topic)
        else:
            query = query + " where topicId like '%%%s%%'" %(topic)
    cur.execute(query)
    data = json.dumps(cur.fetchall())
    cur.close()
    openconnection.commit()
    return data

def getQuestionIds(topicList, openconnection):
    """
    :param topicList: List of topicid
    :param openconnection:
    :return: List of all questionid related to the topics in topicid
    """
    cur = openconnection.cursor()
    query = "select * from related_topic"
    for topic in topicList:
        if query.find("where") > 0:
            query = query + " or topicId like '%%%s%%'" % (topic)
        else:
            query = query + " where topicId like '%%%s%%'" % (topic)

    cur.execute(query)
    questions_topics = cur.fetchall()
    question_ids = []
    for question_topic in questions_topics:
        question_ids.append(question_topic[0])

    cur.close()
    openconnection.commit()
    return question_ids

def filterQuestions(topicList, openconnection):
    """
    :param topicList: List of topicid
    :param openconnection:
    :return: JSON object with all questions related to the topics in topicList
    """
    question_ids = getQuestionIds(topicList,openconnection)
    cur = openconnection.cursor(cursor_factory=RealDictCursor)
    query = "select * from question"

    for question in question_ids:
        if query.find("where") > 0 :
            query = query + " , %d " %(question)
        else:
            query = query + " where questionId in ( %d" %(question)

    if query.find("where")>=0 : query = query + ")"
    cur.execute(query)
    questions = json.dumps(cur.fetchall())
    cur.close()
    openconnection.commit()
    return questions

def filterAnswers(questionId, openconnection):
    """
    :param questionId: Integer questionid
    :param openconnection:
    :return: JSON object with all answers related the the questionId
    """
    cur = openconnection.cursor(cursor_factory=RealDictCursor)
    query = "select * from answer where questionId=%d" %(int(questionId))
    cur.execute(query)
    answers = json.dumps(cur.fetchall())
    cur.close()
    openconnection.commit()
    return answers

# Uncomment following to call the methods
# con = getOpenConnection()
# topics_list = ["abdominal","acute"]
# topic_ids = ['abdominal-hernia-questions','abdominal-muscles-questions','acute-bronchitis-questions']
# questionId = 5000996
# con = getOpenConnection()
# topics = filterTopics(topics_list, con)
# questions =  filterQuestions(topic_ids,con)
# answers = filterAnswers(questionId, con)
# print (topics)
# print (questions)
# print (answers)
