import csv
import json
from collections import OrderedDict
import os 
dir_path = os.path.dirname(os.path.realpath(__file__))

question_topic_matrix_file = dir_path + "/../item_based_collaborative_filtering/question_topic_matrix.csv"
data_ibs_file = dir_path + "/../item_based_collaborative_filtering/data_ibs.csv"

def getTopics():
    with open(question_topic_matrix_file, 'r') as data_file:
        reader = csv.reader(data_file, delimiter='\t')
        for row in reader:
            topics = row[0].split(',')
            break
    del(topics[0])
    return topics

def getTopicIndex(topics, topicId):
    # Find index of given topic id
    for topics_index in range(len(topics)):
        if topics[topics_index] == topicId:
            break
    return topics_index

def getSimilarityScores(topics_index):
    # Get its corresponding similarity scores
    index = 0
    similarity_scores = []
    with open(data_ibs_file, 'r') as data_file:
        reader = csv.reader(data_file, delimiter='\t')
        for row in reader:
            if (index == topics_index):
                similarity_scores = row[0].split(',')
                break
            index = index + 1
    return similarity_scores

def getRecommendedTopics(topicId):
    topics = getTopics()
    topics_index = getTopicIndex(topics, topicId)
    similarity_scores = getSimilarityScores(topics_index)
    sorted_similarity_scores_index = list(reversed(sorted(range(len(similarity_scores)), key=lambda k: similarity_scores[k])))[:10]

    recommended_topics_dict = OrderedDict()
    for similarity_index in sorted_similarity_scores_index:
        if similarity_scores[similarity_index]!='0.0':
            recommended_topics_dict[topics[similarity_index]] = similarity_scores[similarity_index]

    recommended_topics = json.dumps(recommended_topics_dict)
    return recommended_topics

# getRecommendedTopics("abdominal-muscles-questions")
# getData("drug-questions")