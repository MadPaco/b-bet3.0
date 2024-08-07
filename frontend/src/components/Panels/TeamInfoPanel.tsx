import { useEffect, useState } from 'react';
import Panel from '../common/Panel';
import { useAuth } from '../auth/AuthContext';
import { fetchTeamInfo, fetchTeamStats, fetchDivisionStandings } from '../../utility/api';
import { TeamInfo } from '../../utility/types';

interface TeamInfoPanelProps { }

const TeamInfoPanel: React.FC<TeamInfoPanelProps> = () => {
  const [teamInfo, setTeamInfo] = useState<TeamInfo | null>(null);
  const [teamStats, setTeamStats] = useState<string | null>(null);
  const [placeInDivision, setPlaceInDivision] = useState<number | null>(null);
  const { favTeam } = useAuth();

  useEffect(() => {
    if (favTeam) {
      fetchTeamInfo(favTeam)
        .then((data) => {
          setTeamInfo(data)
        })
        .catch((error) => console.error(error));
    }
  }, [favTeam]);

  //fetch division standings
  useEffect(() => {
    if (teamInfo) {
      fetchDivisionStandings(teamInfo.conference, teamInfo.division)
        .then((data) => {
          const teamIndex = data.findIndex((team) => team.name === teamInfo.name);
          if (teamIndex !== -1) {
            setPlaceInDivision(teamIndex + 1);
          } else {
            setPlaceInDivision(null); // If team is not found
          }
        })
        .catch((error) => console.error(error));
    }
  }, [teamInfo]);

  //fetching team stats
  useEffect(() => {
    if (favTeam) {
      fetchTeamStats(favTeam)
        .then((data) => setTeamStats(data))
        .catch((error) => console.error(error));
    }
  }, [favTeam]);

  return (
    <Panel>
      {teamInfo ? (
        <div className="flex items-center flex-col">
          <div>
            <p className='text-xl font-semibold mb-2' >{teamInfo.name} - {teamInfo.conference} {teamInfo.division}</p>
          </div>
          <div className='flex items-center'>
            <div >
              <img
                src={`/assets/images/teams/${teamInfo.logo}`}
                alt={teamInfo.name}
                className="w-24 h-24 object-contain mr-4"
              />
            </div>

            <div className="flex items-center">
              <div>
                {teamStats && <p>Record: {teamStats.wins} - {teamStats.losses} {teamStats.ties > 0 ? '-' + teamStats.ties : ''}</p>}
                {teamStats && <div>
                  <p></p>
                  <p>Points Scored: {teamStats.pointsFor}</p>
                  <p>Points Against: {teamStats.pointsAgainst}</p>
                  <p>Point Differential: {teamStats.netPoints > 0 ? `+${teamStats.netPoints}` : `${teamStats.netPoints}`}</p>
                  <p>Place in division: {placeInDivision}</p>
                </div>}
              </div>
            </div>
          </div>
        </div>
      ) : (
        <p>Loading...</p>
      )}
    </Panel>
  );
};

export default TeamInfoPanel;
