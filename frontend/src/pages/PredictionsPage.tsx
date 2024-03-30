import LoggedInLayout from '../components/layout/LoggedInLayout';
import { useColor } from '../context/ColorContext';
import { fetchSchedule, addBets, fetchBets } from '../utility/api';
import { colorClasses } from '../data/colorClasses';
import { useAuth } from '../components/auth/AuthContext';
import { useState, useEffect } from 'react';

const PredictionsPage: React.FC = () => {
  const { username } = useAuth();
  const NFLWEEKS = 22;
  const [schedule, setSchedule] = useState<Game[]>([]);
  const [predictions, setPredictions] = useState<Prediction[]>([]);
  const [weekNumber, setWeekNumber] = useState(1);
  const { primaryColor } = useColor();
  const colorClass = primaryColor
    ? colorClasses[primaryColor as keyof typeof colorClasses]
    : 'bg-gray-400 hover:bg-gray-300';

  type Game = {
    id: number;
    weekNumber: number;
    date: string;
    location: string;
    homeTeam: string;
    awayTeam: string;
    homeTeamLogo: string;
    awayTeamLogo: string;
    homeOdds: number;
    awayOdds: number;
    overUnder: number;
  };

  type Prediction = {
    gameID: number;
    awayPrediction: number | null;
    homePrediction: number | null;
  };

  const options = [];
  for (let i = 1; i <= NFLWEEKS; i++) {
    options.push(<option value={i}>Week {i}</option>);
  }

  useEffect(() => {
    const getSchedule = async () => {
      const response = await fetchSchedule(weekNumber);
      const data = await response.json();
      setSchedule(data);
    };

    getSchedule();
  }, [weekNumber]);

  useEffect(() => {
    const getInitialPrediction = async () => {
      const response = await fetchBets(weekNumber, username);
      const data = await response.json();
      setPredictions(data);
    };
    getInitialPrediction();
  }, [weekNumber]);

  const handlePredictionChange = (
    gameID: number,
    value: number,
    type: string,
  ) => {
    const predictionIndex = predictions.findIndex((p) => p.gameID === gameID);

    if (predictionIndex !== -1) {
      setPredictions((prev) =>
        prev.map((p, i) =>
          i === predictionIndex ? { ...p, [type]: value || null } : p,
        ),
      );
    } else {
      setPredictions((prev) => [
        ...prev,
        {
          gameID,
          homePrediction: type === 'homePrediction' ? value || null : null,
          awayPrediction: type === 'awayPrediction' ? value || null : null,
        },
      ]);
    }
  };

  const handleSubmit = (predictions: Prediction[]) => {
    const response = addBets(predictions);
    return response;
  };

  return (
    <LoggedInLayout>
      <div
        className={`flex flex-col lg:pt-10 px-10 text-white items-center align-middle justify-center `}
      >
        <div className={` ${colorClass}  w-1/2 rounded-md`}>
          <form
            className="flex flex-col justify-center items-center"
            onSubmit={(e) => {
              e.preventDefault();
              handleSubmit(predictions);
            }}
          >
            <select
              className="w-1/5 mt-3 bg-gray-600 text-white p-2 rounded-lg border-none focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-opacity-50"
              value={weekNumber}
              onChange={(e) => setWeekNumber(Number(e.target.value))}
            >
              {options}
            </select>
            {schedule.map((game) => (
              <div
                key={game.id}
                className={`${colorClass} game flex flex-col items-center bg-opacity-70 rounded-lg p-3 m-3`}
              >
                <div className="time text-lg font-bold">{game.date}</div>
                <div className="teams lg:text-lg flex items-center py-3">
                  <img
                    className="h-7 w-7 lg:h-10 lg:w-10 mr-3"
                    src={`/assets/images/teams/${game.awayTeamLogo}`}
                    alt={game.awayTeam}
                  />
                  <div className="flex flex-col items-center">
                    <span>{game.awayTeam}</span>
                    <input
                      onChange={(e) =>
                        handlePredictionChange(
                          game.id,
                          Number(e.target.value),
                          'awayPrediction',
                        )
                      }
                      name="awayPrediction"
                      type="number"
                      className="w-1/4 text-black"
                      value={
                        predictions.find((p) => p.gameID === game.id)
                          ?.awayPrediction || ''
                      }
                    ></input>
                  </div>

                  <span className="mx-2"> at </span>
                  <div className="flex flex-col items-center">
                    <span>{game.homeTeam}</span>
                    <input
                      onChange={(e) =>
                        handlePredictionChange(
                          game.id,
                          Number(e.target.value),
                          'homePrediction',
                        )
                      }
                      value={
                        predictions.find((p) => p.gameID === game.id)
                          ?.homePrediction || ''
                      }
                      name="homePrediction"
                      type="number"
                      className="w-1/4 text-black"
                    ></input>
                  </div>

                  <img
                    className="h-7 w-7 lg:h-10 lg:w-10 ml-3"
                    src={`/assets/images/teams/${game.homeTeamLogo}`}
                    alt={game.homeTeam}
                  />
                </div>
                <div className="odds mb-2 flex items-center text-sm">
                  <span className="flex flex-col mr-3 items-center">
                    <span>away odds</span>
                    <span>({game.awayOdds})</span>
                  </span>
                  <span className="flex flex-col mx-3 items-center">
                    <span>over/under</span>
                    <span>({game.overUnder})</span>
                  </span>{' '}
                  <span className="flex flex-col ml-3 items-center">
                    <span>home odds</span>
                    <span>({game.homeOdds})</span>
                  </span>
                </div>
                <div className="location">{game.location}</div>
              </div>
            ))}
            <button type="submit">Submit</button>
          </form>
        </div>
      </div>
    </LoggedInLayout>
  );
};
export default PredictionsPage;
