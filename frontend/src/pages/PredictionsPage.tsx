import LoggedInLayout from '../components/layout/LoggedInLayout';
import { fetchSchedule, addBets, fetchBets, getCurrentWeek as fetchCurrentWeek } from '../utility/api';
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
  const [weekNumber, setWeekNumber] = useState<number | null>(null);

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
    const getCurrentWeek = async () => {
      const response = await fetchCurrentWeek();
      setWeekNumber(response.currentWeek);
    };

    getCurrentWeek();
  }, []);

  useEffect(() => {
    const getSchedule = async (week: number) => {
      const data = await fetchSchedule(week);
      setSchedule(data);

      setPredictions((prevPredictions) => {
        const newPredictions = data.map((game) => {
          const existingPrediction = prevPredictions.find((p) => p.gameID === game.id);
          return existingPrediction || { gameID: game.id, awayPrediction: null, homePrediction: null };
        });
        return newPredictions;
      });
    };

    if (weekNumber !== null) {
      getSchedule(weekNumber);
    }
  }, [weekNumber]);

  useEffect(() => {
    const getInitialPrediction = async () => {
      const data = await fetchBets(weekNumber, username);
      setPredictions(data);
    };

    if (weekNumber !== null) {
      getInitialPrediction();
    }
  }, [weekNumber, username]);

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
              value={weekNumber || 1}
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
                  className='bg-gray-900 m-3 flex flex-col items-center bg-opacity-90 rounded-lg py-2 lg:p-2 w-full border-solid border-2 border-highlightCream'
                >
                  <div className="text-lg font-bold mb-2 text-highlightGold text-center">{new Intl.DateTimeFormat('en-GB', { weekday: 'short', year: 'numeric', month: 'long', day: '2-digit', hour: '2-digit', minute: '2-digit' }).format(new Date(game.date))}</div>
                  <div className="flex items-center justify-center py-3 w-full">
                    <div className='w-1/5 lg:p-4'>
                      <img
                        className="mb-2"
                        src={`/assets/images/teams/${game.awayTeamLogo}`}
                        alt={game.awayTeam}
                      />
                    </div>
                    <div className='w-3/5 flex items-center justify-center'>
                      <div className="flex flex-col items-center mx-3">

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
                          className="w-10 text-black text-center mt-2 p-1 rounded-md focus:animate-pulseGlow"
                          value={
                            predictions.find((p) => p.gameID === game.id)?.awayPrediction ?? ''
                          }
                        />
                      </div>

                      <span className="mx-5 text-3xl font-bold">at</span>

                      <div className="flex flex-col items-center mx-3">
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
                          className="w-10 text-black text-center mt-2 p-1 rounded-md focus:animate-pulseGlow"
                          value={
                            predictions.find((p) => p.gameID === game.id)?.homePrediction ?? ''
                          }
                        />
                      </div>
                    </div>

                    <div className='w-1/5 lg:p-4'>
                      <img
                        className="mb-2"
                        src={`/assets/images/teams/${game.homeTeamLogo}`}
                        alt={game.homeTeam}
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
