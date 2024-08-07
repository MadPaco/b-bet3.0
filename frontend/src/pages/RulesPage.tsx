import LoggedInLayout from '../components/layout/LoggedInLayout';

const RulesPage: React.FC = () => {
  return (
    <LoggedInLayout>
      <div className="flex flex-col w-full p-2 items-center">
        <div className="text-highlightCream lg:p-2 text-center flex flex-col items-center w-full my-3">
          <h1 className='my-3 text-highlightGold text-xl font-bold text-shadow-sm shadow-black'>Rules, FAQ and Notes</h1>
          <div className='bg-gray-900 m-3 p-4 w-full lg:w-2/5 text-left rounded-xl border-2 border-highlightCream bg-opacity-90'>
            <h1 className='font-bold text-highlightGold'>Rules</h1>
            <ul className='list-disc p-2'>
              <li>
                A correct bet (meaning that home and away score were
                correct) gives 5 points
              </li>
              <li>
                The correct margin (picking the right winner and the
                difference is correct but the results were off) gives 3
                points
              </li>
              <li>Picking the right winner gives 1 point</li>
              <li>Preseason predictions award the specified amount of points</li>
              <li>Achievements are purely cosmetic and yield no points</li>
              <li>At the end of the season, the user with the most points wins</li>
            </ul>
          </div>
          <div className='bg-gray-900 m-3 p-2 w-full lg:w-2/5 text-left rounded-xl border-2 border-highlightCream bg-opacity-90'>
            <h1 className='mb-3 font-bold text-highlightGold'>FAQ</h1>
            <p className='mb-3 '>
              <strong>Q: Warum ist alles auf englisch?</strong><br />
              A: Weil ich Lisa heisse, ein Jahr in Australien war und auf Deutschland garnicht mehr klarkomme.
            </p>
            <p className='mb-3 '>
              <strong>Q: Im ernst?</strong><br />
              A: Nein. Code und Kommentare darin sind immer auf englisch und mir ist erst nach der Hälfte aufgefallen, dass ich sämtliche Texte auf deutsch hätte schreiben sollen. Eine Möglichkeit zur Übersetzung kommt mit dem ersten grossen in-season update. Versprochen.
            </p>
            <p className='mb-3 '>
              <strong>Q: Why does everything look different?</strong><br />
              A: Because I redid everything from scratch. I used this to give the page a more modern look. I hope you like it. If you are interested in the details as of why, check out the personal explanations section further down.
            </p>
            <p className='mb-3'>
              <strong>Q: What is a hit?</strong><br />
              A: A hit is when you score at least 1 point, meaning you correctly predicted the winner.
            </p>

            <p className='mb-3'>
              <strong>Q: What is hitrate?</strong><br />
              A: The percentage of predictions where you scored atleast 1 point.
            </p>
            <p className='mb-3'>
              <strong>Q: Until when can I place a bet?</strong><br />
              A: Until kickoff.
            </p>
            <p className='mb-3'>
              <strong>Q: When can I see the bets of other players?</strong><br />
              A: Once the game started and all bets are locked.
            </p>
            <p className='mb-3 '>
              <strong>Q: Until when can I place my preseason predictions?</strong><br />
              A: Until the very first game of the regular season starts, 06.09.2024 02:20.
            </p>
            <p className='mb-3 '>
              <strong>Q: Why are the scores not updated in real time?</strong><br />
              A: APIs are expensive. Maybe in the future.
            </p>
            <p className='mb-3 '>
              <strong>Q: So when are the scores updated then?</strong><br />
              A: Usually on Tuesday morning so I can update all games in one go. But I might update partial weeks if I feel like it. 🙃
            </p>
            <p className='mb-3 '>
              <strong>Q: The Team Hit Rate vs. Point Average Chart is cluttered, can you fix this?</strong><br />
              A: This will become less of an issue once we played several weeks and values start to spread. You can always click on a data point to see all the teams in this point.
            </p>
            <p className='mb-3 '>
              <strong>Q: I forgot my password. Can you tell me what it was?</strong><br />
              A: No. But I can set a new one. Shoot me a message on Whatsapp.
            </p>
            <p className='mb-3 '>
              <strong>Q: Why is the banner on the dashboard so weird?</strong><br />
              A: It's AI generated. I am also not very happy with them but it looks way better than no banner. They are subject to change tho.
            </p>
            <p className='mb-3 '>
              <strong>Q: Speaking of dashboard, why do the colors in it differ from the rest of the page?</strong><br />
              A: Your dashboard colors depend on your favorite NFL team. I though this is a nice touch. I might add functionality to change the color scheme down the road.
            </p>
          </div>
          <div className='bg-gray-900 m-3 p-4 w-full lg:w-2/5 text-left rounded-xl border-2 border-highlightCream bg-opacity-90'>
            <h1 className='font-bold mb-3 text-highlightGold'>Version 3.1 roadmap</h1>
            <p className='mb-3 '>
              <strong>Translation</strong><br />
              Implement a way to change languages smoothly.
            </p>
            <p className='mb-3 '>
              <strong>Reset Password via email</strong><br />
              This had to be scratched because of time issues. I know this is an important feature so I will implement this asap.
            </p>
            <p className='mb-3 '>
              <strong>Any Changes you deem urgent </strong><br />
              Hit me up if you think there is a very important feature missing.
            </p>

            <h1 className='font-bold mb-3 text-highlightGold'>Version 3.2 roadmap</h1>
            <p className='mb-3 '>
              <strong>Achievement Showcase</strong><br />
              Choose achievements to present on your profile.
            </p>
            <p className='mb-3 '>
              <strong>Glowing name for current leader</strong><br />
              The current leader deserves some praise.
            </p>

            <h1 className='font-bold mb-3 text-highlightGold'>Version 3.3 roadmap</h1>
            <p className='mb-3 '>
              <strong>Performance improvements</strong><br />
              I am writing this while still in the dev environment. If the performance is too bad, this might get pushed to an earlier update.
            </p>
          </div>

          <div className='bg-gray-900 m-3 p-4 w-full lg:w-2/5 text-left rounded-xl border-2 border-highlightCream bg-opacity-90'>
            <h1 className='font-bold mb-3 text-highlightGold'>Some personal explanations</h1>
            <p className='mb-3 '>
              <strong>
                This section is about some technical details and personal rambling. So if you are not interested in this there is nothing else for you on this page. Good luck with your predictions.
              </strong>
            </p>
            <p className='mb-3'>
              <strong>The switch</strong><br />
              As you might or not know, the previous Version was written with Django. This was great to get the page up and running fast. But it kind of felt like a shortcut.
              Furthernmore, I wanted to switch to more commonly used frameworks.
            </p>
            <p>Since ~78% of all websites use php to some degree, I wanted to switch to a php backend, so I used symfony for this.
              For the frontend, I switched to React together with Typescript and Tailwindcss.</p>
            <p className='mb-3'>
              Also, the old codebase was a hot mess. The new one includes over 200 tests for the backend alone, so I feel much more confident that I delivered a (mostly) bug free experience for all of you.
              This also allows me to reuse the code more easily the next years.
            </p>
            <p className='mb-3 '>
              <strong>What's next?</strong><br />
              I will use this years version to build upon. Your feedback will allow me to improve the page more.
              My goal is to wrap everything that is neccessary in a docker container to
              allow other people to setup their own instance of Bbet.
            </p>
          </div>
        </div>
      </div>
    </LoggedInLayout>
  );
};

export default RulesPage;
