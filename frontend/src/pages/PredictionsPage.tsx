import LoggedInLayout from '../components/layout/LoggedInLayout';
import { fetchSchedule, addBets, fetchBets } from '../utility/api';
import { useAuth } from '../components/auth/AuthContext';
import { useState, useEffect } from 'react';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faCheck } from '@fortawesome/free-solid-svg-icons';
import { generateWeekOptions } from '../data/weekLabels';

const PredictionsPage: React.FC = () => {
  const { username } = useAuth();
  const [schedule, setSchedule] = useState<Game[]>([]);
  const [predictions, setPredictions] = useState<Prediction[]>([]);
  const [showPopup, setShowPopup] = useState(false);
  const [weekNumber, setWeekNumber] = useState(1);

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

  const weeks = generateWeekOptions();

  useEffect(() => {
    const getSchedule = async () => {
      const response = await fetchSchedule(weekNumber);
      const data = await response.json();
      setSchedule(data);

      setPredictions((prevPredictions) => {
        const newPredictions = data.map((game) => {
          const existingPrediction = prevPredictions.find((p) => p.gameID === game.id);
          return existingPrediction || { gameID: game.id, awayPrediction: null, homePrediction: null };
        });
        return newPredictions;
      });
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

  const handlePredictionChange = (gameID: number, value: number, type: 'awayPrediction' | 'homePrediction') => {
    setPredictions((prev) => {
      const existingPrediction = prev.find((p) => p.gameID === gameID);
      if (existingPrediction) {
        return prev.map((p) =>
          p.gameID === gameID ? { ...p, [type]: value || null } : p,
        );
      } else {
        return [...prev, { gameID, awayPrediction: type === 'awayPrediction' ? value : null, homePrediction: type === 'homePrediction' ? value : null }];
      }
    });
  };

  const handleSubmit = async (predictions: Prediction[]) => {
    await addBets(predictions);
    setShowPopup(true);
    setTimeout(() => setShowPopup(false), 1000);
  };

  return (
    <LoggedInLayout>
      <div className="flex flex-col p-2 text-white items-center justify-center min-h-screen">
        <div className='w-full lg:w-1/3 rounded-xl shadow-lg'>
          <form
            className="flex flex-col justify-center items-center"
            onSubmit={(e) => {
              e.preventDefault();
              handleSubmit(predictions);
            }}
          >
            <h1 className='my-3 text-highlightGold text-xl font-bold text-shadow-sm shadow-black'>Predictions</h1>
            <select
              className="bg-gray-900 text-white p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-highlightGold focus:ring-opacity-50 border-highlightCream border-solid border-2"
              value={weekNumber}
              onChange={(e) => setWeekNumber(Number(e.target.value))}
            >
              {weeks}
            </select>
            {schedule.length === 0 ? (
              <div className="text-center mt-10 text-gray-400">
                No games scheduled for this week.
              </div>
            ) : (
              schedule.map((game) => (
                <div
                  key={game.id}
                  className='bg-gray-900 m-3 flex flex-col items-center bg-opacity-90 rounded-lg p-2 w-full border-solid border-2 border-highlightCream'
                >
                  <div className="text-lg font-bold mb-2 text-highlightGold text-center">{new Intl.DateTimeFormat('en-GB', { weekday: 'short', year: 'numeric', month: 'long', day: '2-digit', hour: '2-digit', minute: '2-digit' }).format(new Date(game.date))}</div>
                  <div className="flex items-center justify-center py-3 w-full">
                    <div className="flex flex-col items-center mx-3">
                      <img
                        className="h-12 w-12 mb-2"
                        src={`/assets/images/teams/${game.awayTeamLogo}`}
                        alt={game.awayTeam}
                      />
                      <span className='text-center'>{game.awayTeam}</span>
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
                        className="w-20 text-black text-center mt-2 p-1 rounded-md focus:animate-pulseGlow"
                        value={
                          predictions.find((p) => p.gameID === game.id)?.awayPrediction ?? ''
                        }
                      />
                    </div>

                    <span className="mx-5 text-3xl font-bold">at</span>

                    <div className="flex flex-col items-center mx-3">
                      <img
                        className="h-auto w-12 mb-2"
                        src={`/assets/images/teams/${game.homeTeamLogo}`}
                        alt={game.homeTeam}
                      />
                      <span className='text-center'>{game.homeTeam}</span>
                      <input
                        onChange={(e) =>
                          handlePredictionChange(
                            game.id,
                            Number(e.target.value),
                            'homePrediction',
                          )
                        }
                        name="homePrediction"
                        type="number"
                        className="w-20 text-black text-center mt-2 p-1 rounded-md focus:animate-pulseGlow"
                        value={
                          predictions.find((p) => p.gameID === game.id)?.homePrediction ?? ''
                        }
                      />
                    </div>
                  </div>
                  <div className="odds mb-2 flex items-center justify-center text-sm w-full px-10">
                    <div className="flex flex-col items-center m-2 text-center">
                      <span>away odds</span>
                      <span>({game.awayOdds})</span>
                    </div>
                    <div className="flex flex-col items-center m-2 text-center">
                      <span>over/under</span>
                      <span>({game.overUnder})</span>
                    </div>
                    <div className="flex flex-col items-center m-2 text-center">
                      <span>home odds</span>
                      <span>({game.homeOdds})</span>
                    </div>
                  </div>
                  <div className="location text-sm text-center">{game.location}</div>
                </div>
              ))
            )}
            {schedule.length > 0 && (
              <button type="submit" className="bg-gray-900 px-6 py-2 rounded-lg my-3 shadow hover:bg-gray-700 border-2 border-highlightGold text-highlightGold transition-colors duration-300">
                Submit
              </button>
            )}
          </form>
          {showPopup && (
            <div className="fixed top-0 left-0 w-screen h-screen flex items-center justify-center">
              <div className="bg-green-500 p-4 rounded shadow-lg text-black">
                Saved <FontAwesomeIcon icon={faCheck} />
              </div>
            </div>
          )}
        </div>
      </div>
    </LoggedInLayout>
  );
};

export default PredictionsPage;
