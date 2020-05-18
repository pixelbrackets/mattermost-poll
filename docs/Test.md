# Test

1. See installation guide
1. Run `php bin/console server:start 127.0.0.1:8000`
1. Open http://localhost:8000 in a browser
1. Send request to create a poll
   ```bash
   http POST http://localhost:8000/new < new.json
   ```
1. Send request to vote for an answer
   ```bash
   http POST http://localhost:8000/vote < vote.json
   ```
1. Send request to end a poll
   ```bash
   http POST http://localhost:8000/close < close.json
   ```
