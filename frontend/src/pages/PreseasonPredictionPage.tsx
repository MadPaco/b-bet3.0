import FormCard from '../components/form/FormCard';
import LoggedInLayout from '../components/layout/LoggedInLayout';
import nflTeams from '../data/nflTeams';
import afcTeams from '../data/afcTeams';
import nfcTeams from '../data/nfcTeams';
import { useState, useEffect } from 'react';
import { useAuth } from '../components/auth/AuthContext';
import TeamSelect from '../components/form/TeamSelect';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faCheck } from '@fortawesome/free-solid-svg-icons';
import { fetchPreseasonPrediction, addPreseasonPrediction } from '../utility/api';

const PreseasonPredictionsPage: React.FC = () => {
  const { username } = useAuth();
  const [showPopup, setShowPopup] = useState(false);
  const [afcChampion, setAfcChampion] = useState<string | null>(null);
  const [nfcChampion, setNfcChampion] = useState<string | null>(null);
  const [superBowlChampion, setSuperBowlChampion] = useState<string | null>(null);
  const [mostPassingYards, setMostPassingYards] = useState<string | null>(null);
  const [mostRushingYards, setMostRushingYards] = useState<string | null>(null);
  const [firstPick, setfirstPick] = useState<string | null>(null);
  const [mostPointsScored, setMostPointsScored] = useState<string | null>(null);
  const [fewestPointsAllowed, setFewestPointsAllowed] = useState<string | null>(null);
  const [highestMarginOfVictory, setHighestMarginOfVictory] = useState<string | null>(null);
  const [teamWithOROY, setTeamWithOROY] = useState<string | null>(null);
  const [teamWithDROY, setTeamWithDROY] = useState<string | null>(null);
  const [teamWithMVP, setTeamWithMVP] = useState<string | null>(null);

  useEffect(() => {
    // fetch predictions
    const fetchPredictions = async () => {
      const response = await fetchPreseasonPrediction(username || '');
      const data = await response.json();
      console.log('Fetched data:', data);
      setAfcChampion(data.afcChampion);
      setNfcChampion(data.nfcChampion);
      setSuperBowlChampion(data.superBowlChampion);
      setMostPassingYards(data.mostPassingYards);
      setMostRushingYards(data.mostRushingYards);
      setfirstPick(data.firstPick);
      setMostPointsScored(data.mostPointsScored);
      setFewestPointsAllowed(data.fewestPointsAllowed);
      setHighestMarginOfVictory(data.highestMarginOfVictory);
      setTeamWithOROY(data.teamWithOROY);
      setTeamWithDROY(data.teamWithDROY);
      setTeamWithMVP(data.teamWithMVP);
    }
    fetchPredictions();
  }, [username]);

  const handleSubmit = async () => {
    const predictions = {
      afcChampion,
      nfcChampion,
      superBowlChampion,
      mostPassingYards,
      mostRushingYards,
      firstPick,
      mostPointsScored,
      fewestPointsAllowed,
      highestMarginOfVictory,
      teamWithOROY,
      teamWithDROY,
      teamWithMVP,
    };
    await addPreseasonPrediction(username || '', predictions);
    setShowPopup(true);
    setTimeout(() => setShowPopup(false), 1000);
  }


  return (
    <LoggedInLayout>
      <div className="flex flex-col px-5 text-white items-center pt-5 min-h-screen text-center">
        <div className='bg-gray-900 p-2 rounded-xl border-2 border-highlightCream bg-opacity-90'>
          <h1 className=' text-highlightGold text-xl font-bold text-shadow-sm shadow-black'>Preseason Predictions</h1>
          <p className='text-highlightCream'>Here you can place several predictions for the season. They will be evaluated at the end of the season. <br /> Good luck!</p>
        </div>

        <div className='flex mt-3 border-2 border-highlightCream rounded-xl'>
          <FormCard>
            <TeamSelect
              label="AFC Champion"
              points={3}
              value={afcChampion}
              onChange={(e) => setAfcChampion(e.target.value)}
              options={afcTeams}
            />
            <TeamSelect
              label="NFC Champion"
              points={3}
              value={nfcChampion}
              onChange={(e) => setNfcChampion(e.target.value)}
              options={nfcTeams}
            />
            <TeamSelect
              label="Super Bowl Champion"
              points={5}
              value={superBowlChampion}
              onChange={(e) => setSuperBowlChampion(e.target.value)}
              options={nflTeams}
            />
            <TeamSelect
              label="Most Passing Yards"
              points={5}
              value={mostPassingYards}
              onChange={(e) => setMostPassingYards(e.target.value)}
              options={nflTeams}
            />
            <TeamSelect
              label="Most Rushing Yards"
              points={5}
              value={mostRushingYards}
              onChange={(e) => setMostRushingYards(e.target.value)}
              options={nflTeams}
            />
            <TeamSelect
              label={"First Overall Pick Next Year"}
              points={3}
              value={firstPick}
              onChange={(e) => setfirstPick(e.target.value)}
              options={nflTeams}
            />
          </FormCard>
          <FormCard>

            <TeamSelect
              label="Most Points Scored"
              points={5}
              value={mostPointsScored}
              onChange={(e) => setMostPointsScored(e.target.value)}
              options={nflTeams}
            />
            <TeamSelect
              label="Fewest Points Allowed"
              points={5}
              value={fewestPointsAllowed}
              onChange={(e) => setFewestPointsAllowed(e.target.value)}
              options={nflTeams}
            />
            <TeamSelect
              label="Highest Margin of Victory"
              points={5}
              value={highestMarginOfVictory}
              onChange={(e) => setHighestMarginOfVictory(e.target.value)}
              options={nflTeams}
            />
            <TeamSelect
              label="Team with OROY"
              points={3}
              value={teamWithOROY}
              onChange={(e) => setTeamWithOROY(e.target.value)}
              options={nflTeams}
            />
            <TeamSelect
              label="Team with DROY"
              points={3}
              value={teamWithDROY}
              onChange={(e) => setTeamWithDROY(e.target.value)}
              options={nflTeams}
            />
            <TeamSelect
              label="Team with MVP"
              points={3}
              value={teamWithMVP}
              onChange={(e) => setTeamWithMVP(e.target.value)}
              options={nflTeams}
            />
          </FormCard></div>
        <button onClick={handleSubmit} className="bg-gray-900 px-6 py-2 rounded-lg my-3 shadow hover:bg-gray-700 border-2 border-highlightGold text-highlightGold transition-colors duration-300">Submit</button>
        {showPopup && (
          <div className="fixed top-0 left-0 w-screen h-screen flex items-center justify-center">
            <div className="bg-green-500 p-4 rounded shadow-lg text-black">
              Saved <FontAwesomeIcon icon={faCheck} />
            </div>
          </div>
        )}
      </div>
    </LoggedInLayout>
  );
};

export default PreseasonPredictionsPage;
