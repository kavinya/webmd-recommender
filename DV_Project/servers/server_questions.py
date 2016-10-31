#!/Users/Kavinya/.pyenv/versions/3.5.2/bin/python

import asyncio
import websockets
import filter_methods

async def answers(websocket, path):
    """
    :param websocket:
    :param path:
    :return:
    Receive questionid from client and sends all answers associated with it as JSON
    """
    question_id = await websocket.recv()
    con = filter_methods.getOpenConnection()
    answer = filter_methods.filterAnswers(question_id, con)
    print("< {}".format(question_id))
    print("> {}".format(answer))
    await websocket.send(answer)

def startAnswers(handler, port):
    start_server = websockets.serve(handler, 'localhost', port)
    asyncio.get_event_loop().run_until_complete(start_server)
    asyncio.get_event_loop().run_forever()

startAnswers(answers,8767)