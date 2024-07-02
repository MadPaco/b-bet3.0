import mysql.connector
from dotenv import load_dotenv
import os

dotenv_path = os.path.join(os.path.dirname(__file__), 'backend', '.env')
load_dotenv(dotenv_path)

db = mysql.connector.connect(
    host=os.getenv("DB_HOST"),
    user=os.getenv("DB_USER"),
    password=os.getenv("DB_PASSWORD"),
    database=os.getenv("DB_NAME")
)

cursor = db.cursor()

# Achievements data
achievements = [
    (
        'First Down',
        'Correctly predict your first game',
        'firstDown.webp',
        "Congratulations on your first down! You've made your first correct prediction. Keep the momentum going!",
        'Amount of Predictions'
    ),
    (
        "Seasoned Pro",
        "Place 50 predictions",
        "seasonedPro.webp",
        "You're getting the hang of this! With 50 predictions under your belt, you're now a seasoned pro.",
        'Amount of Predictions'
    ),
    (
        "Expert",
        "Place 100 predictions",
        "expert.webp",
        "100 predictions! You're officially an expert in NFL predictions.",
        'Amount of Predictions'
    ),
    (
        "Gridiron Guru",
        "Place 200 predictions",
        "gridironGuru.webp",
        "200 predictions! You're a true gridiron guru, with knowledge as deep as the playbook.",
        "Amount of Predictions"
    ),
    (
        "Hall of Famer",
        "Predict every game of the regular season",
        "hallOfFamer.webp",
        "You've predicted every game of the regular season. Welcome to the Hall of Fame!",
        "Amount of Predictions"
    ),
    (
        "Early Bird",
        "Place all predictions for a week at least 24h before the first kickoff",
        "earlyBird.webp",
        "The early bird catches the worm! You made all your predictions well in advance.",
        "Timing and Strategy"
    ),
    (
        "Two-Minute Drill",
        "Place a prediction at most 2 minutes before kickoff and hit on it",
        "twoMinuteDrill.webp",
        "Two Minute Drill ala Brady!",
        "Timing and Strategy"
    ),
    (
        "Trick Play",
        "Change a prediction at least 4 times and hit on it",
        "trickPlay.webp",
        "PHILLY PHILLY",
        "Timing and Strategy"
    ),
    (
        "Pigskin Prophet",
        "Hit on all games in a week",
        "pigskinProphet.webp",
        "All-seeing, all-knowing. The Pigskin Prophet!",
        "Weekly and Cumulative Performance"
    ),
    (
        "Touchdown",
        "Score 7 points in a week",
        "touchdown.webp",
        "DIGGS! SIDELINE! TOUCHDOWN! UNBELIEVABLE!",
        "Weekly and Cumulative Performance"
    ),
    (
        "Pick Six",
        "Correctly predict 6 games in a single week",
        "pickSix.webp",
        "Pick Six! Ed Reed would be proud of you.",
        "Weekly and Cumulative Performance"
    ),
    (
        "MVP of the Week",
        "Have the highest score in a week",
        "mvpOfTheWeek.webp",
        "You're the Most Valuable Predictor!",
        "Weekly and Cumulative Performance"
    ),
    (
        "Consistency is Key",
        "Score points in 10 weeks",
        "consistencyIsKey.webp",
        "Like Drew Brees - always on target.",
        "Weekly and Cumulative Performance"
    ),
    (
        "Playoff Push",
        "Score the most points in the last 6 weeks of the regular season",
        "playoffPush.webp",
        "You've made a strong playoff push, will it be enough tho?",
        "Weekly and Cumulative Performance"
    ),
    (
        "Headstart",
        "Score the most points in the first 6 weeks of the regular season",
        "headstart.webp",
        "You've taken an early lead! Now it's time to defend it!",
        "Weekly and Cumulative Performance"
    ),
    (
        "Midseason Form",
        "Score the most points in weeks 7-12",
        "midseasonForm.webp",
        "You're in peak midseason form! Finish strong!",
        "Weekly and Cumulative Performance"
    ),
    (
        "Bowl Game Secured",
        "Hit in more than 50 percent of all regular season games",
        "bowlGameSecured.webp",
        "All this to play in something called Cheez-it Citrus bowl?",
        "Weekly and Cumulative Performance"
    ),
    (
        "Sunday Funday",
        "Hit on every game that is played on Sunday",
        "sundayFunday.webp",
        "You're the real MVP of tailgate parties! Now smash through a table!",
        "Weekly and Cumulative Performance"
    ),
    (
        "Pro Bowler",
        "Score at least 100 points in total",
        "proBowler.webp",
        "You have been voted into the pro bowl, scoring 100 points in total. You're among the best of the best.",
        "Weekly and Cumulative Performance"
    ),
    (
        "All Pro",
        "Score at least 200 points in total",
        "allPro.webp",
        "With 200 points this season, you're in an elite class of predictors.",
        "Weekly and Cumulative Performance"
    ),
    (
        "Consistent Performer",
        "Score at least 10 points in 5 consecutive weeks",
        "consistentPerformer.webp",
        "Coaches love consistency!",
        "Streaks and Trends"
    ),
    (
        "Deep Run",
        "Score at least 10 points in 10 consecutive weeks",
        "deepRun.webp",
        "Are you coached by Mike Tomlin?",
        "Streaks and Trends"
    ),
    (
        "Hot Streak",
        "Score at least 15 points in 3 consecutive weeks",
        "hotStreak.webp",
        "Is this Mahomes in the playoffs?",
        "Streaks and Trends"
    ),
    (
        "Bench Warmer",
        "Score less than 5 points in 5 consecutive weeks",
        "benchWarmer.webp",
        "It's been a tough stretch, but remember, even Brady started as a backup.",
        "Streaks and Trends"
    ),
    (
        "Comeback Kid",
        "Score the most points in a week after scoring the least points the week before",
        "comebackKid.webp",
        "28-3? No problem.",
        "Streaks and Trends"
    ),
    (
        "Slump Buster",
        "End a streak of three weeks with less than 5 points by scoring more than 10 points in a week",
        "slumpBuster.webp",
        "That's more like it! You're still in the race.",
        "Streaks and Trends"
    ),
    (
        "Underdog Lover",
        "Hot on 10 games where you picked the underdog",
        "underdogLover.webp",
        "You love the underdog! And they love you back.",
        "Special Predictions"
    ),
    (
        "Nostradamus",
        "Predict the exact score of a game correctly",
        "nostradamus.webp",
        "Amazing foresight, well deserved 5 points.",
        "Special Predictions"
    ),
    (
        "Upset Specialist",
        "Predict a big upset (-300 odds or worse)",
        "upsetSpecialist.webp",
        "You also knew the Giants would win in 2007 right?",
        "Special Predictions"
    ),
    (
        "Perfectly Balanced",
        "Predict a game that ends in a tie",
        "perfectlyBalanced.webp",
        "As all things should be.",
        "Special Predictions"
    ),
    (
        "Nail-Biter",
        "Correctly predict the margin of 5 games where the margin of victory is 2 points or less",
        "nailBiter.webp",
        "That was close!",
        "Special Predictions"
    ),
    (
        "Blowout-Boss",
        "Correctly predict the margin of 5 games where the margin of victory is 21 points or more",
        "blowoutBoss.webp",
        "Feels like Super Bowl XLVIII",
        "Special Predictions"
    ),
    (
        "Hometown Hero",
        "Predict the outcome of all games of your favorite team",
        "hometownHero.webp",
        "A true fan knows his team - and it's limitations.",
        "Special Predictions"
    ),
    (
        "Super Bowl Prophet",
        "Predict the winner of the Super Bowl",
        "superBowlProphet.webp",
        "Why even watch if you know what's going to happen?",
        "Special Predictions"
    ),
    (
        "Primetime Player",
        "Hit on all games played on Monday Night Football",
        "primetimePlayer.webp",
        "You thrive when the lights shine the brightest!",
        "Special Predictions"
    ),
    (
        "Audible",
        "Change the winner of a game 10 minutes before kickoff and win",
        "audible.webp",
        "Omaha! Omaha!",
        "Special Predictions"
    ),
    (
        "Hail Mary",
        "Change the winner of a game 1 minute before kickoff and win",
        "hailMary.webp",
        "Hail Mary! Full of grace!",
        "Special Predictions"
    ),
    (
        "Bye Week",
        "Score 0 points in a week",
        "byeWeek.webp",
        "You're on a bye week. Rest up and come back stronger!",
        "Mocking Achievements"
    ),
    (
        "Injury Reserve",
        "Dont place any predictions in for 3 weeks",
        "injuryReserve.webp",
        "You're on the injury reserve list. Get well soon!",
        "Mocking Achievements"
    ),
    (
        "Aaron Rodgers 2023",
        "Hit on the Thursday night game, but lose every other game this week",
        "aaronRodgers2023.webp",
        "A short run, but now you can be on podcasts!",
        "Mocking Achievements"
    ),
    (
        "Fumble",
        "Change the winner of a match before the game and lose",
        "fumble.webp",
        "What were you thinking? You fumbled the bag!",
        "Mocking Achievements"
    ),
    (
        "Punt Return",
        "Change the winner of a match before the game and end up with fewer points than before",
        "puntReturn.webp",
        "You tried to make a play, but it backfired.",
        "Mocking Achievements"
    )
]
for name, description, image, flavor_text, category in achievements:
    cursor.execute("INSERT INTO achievement (name, description, image, flavor_text, category) VALUES (%s, %s, %s, %s, %s)", (name, description, image, flavor_text, category))

db.commit()

cursor.close()
db.close()
