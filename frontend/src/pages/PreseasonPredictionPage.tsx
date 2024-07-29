import FormCard from '../components/form/FormCard';
import LoggedInLayout from '../components/layout/LoggedInLayout';
import nflTeams from '../data/nflTeams';
import afcTeams from '../data/afcTeams';
import nfcTeams from '../data/nfcTeams';
import { useState, useEffect } from 'react';
import { useAuth } from '../components/auth/AuthContext';
import TeamSelect from '../components/form/TeamSelect';
import { fetchPreseasonPrediction, addPreseasonPrediction } from '../utility/api';

const PreseasonPredictionsPage: React.FC = () => {
  const { username } = useAuth();
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
  }


  return (
    <LoggedInLayout>
      <div className="flex flex-col px-5 text-white items-center pt-5 min-h-screen text-center">
        <h1>Preseason Prediction</h1>
        <p>Here you can place several predictions for the season. They will be evaluated at the end of the season. <br /> Good luck!</p>
        <div className='flex mt-3'>
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
        <button onClick={handleSubmit} className="mt-3 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Submit</button>
      </div>
    </LoggedInLayout>
  );
};

export default PreseasonPredictionsPage;
