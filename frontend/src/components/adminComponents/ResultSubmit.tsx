import { useEffect, useState } from 'react';
import { fetchSchedule, submitResults, fetchResults } from '../../utility/api';
import { Result, Game } from '../../utility/types';

const ResultSubmit: React.FC = () => {
  const [games, setGames] = useState<Game[]>([]);
  // use the gameID as the key to store the scores
  // this makes updating the game scores easier for me
  const [scores, setScores] = useState<{
    [gameId: number]: { homeTeamScore: number; awayTeamScore: number };
  }>({});
  const [weekNumber, setWeekNumber] = useState(1);
  const NFLWEEKS = 22;

  useEffect(() => {
    const getGames = async () => {
      const games = await fetchSchedule(weekNumber);
      setGames(games);
    };

    getGames();
  }, [weekNumber]);

  useEffect(() => {
    const getResults = async () => {
      const results = await fetchResults(weekNumber);
      const scores = results.reduce(
        (accumulator: Array<Result>, result: Result) => {
          accumulator[Number(result.gameID)] = {
            homeTeamScore: result.homeTeamScore,
            awayTeamScore: result.awayTeamScore,
          };
          return accumulator;
        },
        {},
      );
      setScores(scores);
    };

    getResults();
  }, [weekNumber]);

  const handleSubmit = () => {
    return async (e: React.FormEvent) => {
      e.preventDefault();
      const response = await submitResults(scores);
      console.log(response)
      if (response === 'success') {
        alert('Scores submitted successfully');
      } else {
        console.log(response.status)
        alert('Error submitting scores');
      }
    };
  };

  return (
    <div>
      <select
        onChange={(e) => {
          setWeekNumber(Number((e.target as HTMLSelectElement).value));
          //reset the scores so we only have the current week's scores
          setScores({});
        }}
        className="text-black"
      >
        {Array.from({ length: NFLWEEKS }, (_, i) => i + 1).map((number) => (
          <option key={number} value={number}>
            week {number}
          </option>
        ))}
      </select>
      <div>
        <form onSubmit={handleSubmit()} className="text-white">
          {games.map((game) => (
            <div key={game.id}>
              <p>ID: {game.id}</p>
              <p>{game.date}</p>
              <p>
                {game.awayTeam} at {game.homeTeam}
              </p>
              <input
                className="mx-4 text-black"
                type="number"
                placeholder="Away Team Score"
                value={scores[game.id]?.awayTeamScore || 0}
                onChange={(e) => {
                  setScores({
                    ...scores,
                    [game.id]: {
                      ...scores[game.id],
                      awayTeamScore: Number(e.target.value),
                    },
                  });
                }}
              />
              <input
                type="number"
                className="text-black "
                placeholder="Home Team Score"
                value={scores[game.id]?.homeTeamScore || 0}
                onChange={(e) => {
                  setScores({
                    ...scores,
                    [game.id]: {
                      ...scores[game.id],
                      homeTeamScore: Number(e.target.value),
                    },
                  });
                }}
              />
            </div>
          ))}
          <button type="submit" className="bg-green-400 rounded-lg px-3 mt-3">
            Submit
          </button>
        </form>
      </div>
    </div>
  );
};

export default ResultSubmit;
