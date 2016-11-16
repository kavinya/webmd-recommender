#!/Users/Kavinya/.pyenv/versions/3.5.2/bin/python

import asyncio
import websockets
import filter_methods

async def topics(websocket, path):
    """
    :param websocket:
    :param path:
    :return:
    Receive all desired topicids from client and sends all question associated with those topics as JSON
    """
    topic_ids_recv = await websocket.recv()
    topic_ids =  topic_ids_recv.split(",")
    con = filter_methods.getOpenConnection()
    questions = filter_methods.filterQuestions(topic_ids, con)
    print("< {}".format(topic_ids))
    print("> {}".format(questions))
    await websocket.send(questions)

def startTopics(handler, port):
    start_server = websockets.serve(handler, 'localhost', port)
    asyncio.get_event_loop().run_until_complete(start_server)
    asyncio.get_event_loop().run_forever()

startTopics(topics,8766)