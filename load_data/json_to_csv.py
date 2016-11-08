import json
import csv

CLEAN_JSON_DIR = "../data/clean_json/"
CSV_DIR = "../data/csv/"

def questionData():
    with open(CLEAN_JSON_DIR+'webmd-question-cleaned.json') as data_file:
        data = json.load(data_file)

    f = csv.writer(open(CSV_DIR+"webmd-question.csv", "wb+"))

    # Write CSV Header, If you dont need that, remove this line
    f.writerow(["questionId", "questionTopicId", "questionTitle", "questionMemberId", "questionContent", \
                "questionPostDate","questionURL"])

    for row in data:
        f.writerow([row["questionId"],
                    row["questionTopicId"],
                    row["questionTitle"],
                    row["questionMemberId"],
                    row["questionContent"],
                    row["questionPostDate"],
                    row["questionURL"]])

def answerData():
    with open(CLEAN_JSON_DIR+'webmd-answer-cleaned.json') as data_file:
        data = json.load(data_file)

    f = csv.writer(open(CSV_DIR+"webmd-answer.csv", "wb+"))

    # Write CSV Header, If you dont need that, remove this line
    f.writerow(["answerId", "questionId", "answerQuestionURL", "answerMemberId", "answerContent","answerPostDate", \
                "answerHelpfulNum","answerVoteNum"])

    for row in data:
        f.writerow([row["answerId"],
                    row["questionId"],
                    row["answerQuestionURL"],
                    row["answerMemberId"],
                    row["answerContent"],
                    row["answerPostDate"],
                    row["answerHelpfulNum"],
                    row["answerVoteNum"]])

def relatedTopicData():
    with open(CLEAN_JSON_DIR+'webmd-related_topic-cleaned.json') as data_file:
        data = json.load(data_file)

    f = csv.writer(open(CSV_DIR+"webmd-related_topic.csv", "wb+"))

    # Write CSV Header, If you dont need that, remove this line
    f.writerow(["questionId", "topicId"])

    for row in data:
        f.writerow([row["questionId"],
                    row["topicId"]])

def topicsData():
    with open(CLEAN_JSON_DIR+'webmd-topics-cleaned.json') as data_file:
        data = json.load(data_file)

    f = csv.writer(open(CSV_DIR+"webmd-topics.csv", "wb+"))

    # Write CSV Header, If you dont need that, remove this line
    f.writerow(["topicId", "topicName"])

    for row in data:
        f.writerow([row["topicId"],
                    row["topicName"]])


questionData()
answerData()
relatedTopicData()
topicsData()