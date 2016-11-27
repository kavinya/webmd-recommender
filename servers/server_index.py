#!/Users/Kavinya/.pyenv/versions/3.5.2/bin/python

import asyncio
import websockets
import filter_methods

async def index(websocket, path):
    """
    :param websocket:
    :param path:
    :return:
    Receive list of desired topics and send all associated {topicid, topicname} as JSON
    """
    topics_recv = await websocket.recv()
    topics_list = topics_recv.split(",")
    con = filter_methods.getOpenConnection()
    topics = filter_methods.filterTopics(topics_list, con)
    print("< {}".format(topics_list))
    print("> {}".format(topics))
    await websocket.send(topics)


def startIndex(handler, port):
    start_server = websockets.serve(handler, 'localhost', port)
    asyncio.get_event_loop().run_until_complete(start_server)
    asyncio.get_event_loop().run_forever()

startIndex(index,8765)
