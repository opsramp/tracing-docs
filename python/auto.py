import json
from flask import Flask
from datetime import datetime

app = Flask(__name__)


@app.route('/time')
def index():

    now = datetime.now()

    current_time = now.strftime("%H:%M:%S")

    return json.dumps(current_time)


app.run()



