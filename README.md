# Ultron_Twitch

## About

Ultron_twitch is meant to help streamers monitor their "competition" on a given category over a given period of time.
Twitch displays how many channels are live at insant T on a specific category but does not provide history.
The Ultron should request via Twitch API the list of channels on a given category (with their viewers count, stream title, channel name, language) every five minutes. The app extracts and stores data.

### Project phases

-   Phase 1 : simple file storage
-   Phase 2 : task automation
-   Phase 3 : storing in database
-   Phase 4 : producing charts

### Consider:

-   how to admin the list of categories?
-   how to request several categories (when one game = different categories)

## My dev notes

### Phase 1

#### Creating a custom command

```
php bin/console twitch:collect-viewers
```

=> File created : src/Command/TwitchCollectCommand.php

#### Environment variables

a env.local file is needed at the project's root

```
TWITCH_CLIENT_ID=[your Twitch client_id]
TWITCH_ACCESS_TOKEN=[your token]
```

how to get credentials: https://dev.twitch.tv/docs/authentication/getting-tokens-oauth/#client-credentials-grant-flow

/!\ Security: all .env files should be in gitIgnore to prevent KEY and secret exposure
in .gitingore add _.env_

#### Timezone

Timezone is set in bin > console

#### response sort

(Helix API – Get Streams) :
"Streams are sorted by viewer count in descending order by default."

by default there's a pagination after 25 results => explicitly request 100 res: " /helix/streams route returns 25 results max by request, except if you mention "first=100".

### Phase 2 Automation

#### Task manager

Win + R, type taskschd.msc to open de task planner
Rigth panel => create a new task
trigger => every hour, repeat every 5 min
Action => start a program - program : path to php.exe - arguments : path to project/bin/console + space + command (twitch:collect-viewers) - start in : path to project

Notes : with this method, the task will execute only if my own computer is on
Next step: migrate to a Linux server (VPS) and use Cron instead
