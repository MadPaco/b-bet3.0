import { useEffect, useState } from 'react';
import Panel from '../common/Panel';
import { useAuth } from '../auth/AuthContext';
import { fetchTeamInfo, fetchTeamStats, fetchDivisionStandings } from '../../utility/api';

interface TeamInfo {
  name: string;
  division: string;
  conference: string;
  logo: string;
  primaryColor: string;
}

interface TeamInfoPanelProps {}

const TeamInfoPanel: React.FC<TeamInfoPanelProps> = () => {
  const [teamInfo, setTeamInfo] = useState<TeamInfo | null>(null);
  const [teamStats, setTeamStats] = useState<string | null>(null);
  const [divisionStandings, setDivisionStandings] = useState<string | null>(null);
  const { favTeam } = useAuth();

  useEffect(() => {
    if (favTeam) {
      fetchTeamInfo(favTeam)
        .then((data) => setTeamInfo(data))
        .catch((error) => console.error(error));
    }
  }, [favTeam]);

  //fetch division standings
  useEffect(() => {
    if (teamInfo) {
      fetchDivisionStandings(teamInfo.conference, teamInfo.division)
        .then((data) => console.log(data))
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
        <div className="flex items-center">
          <img
            src={`/assets/images/teams/${teamInfo.logo}`}
            alt={teamInfo.name}
            className="w-24 h-24 object-contain mr-4"
          />
          <div className="flex items-center">
            <div>
              <p>{teamInfo.name}</p>
              <p>
                {teamInfo.conference} - {teamInfo.division}
              </p>
              {teamStats && <div>
                <p>{teamStats.wins} - {teamStats.losses} {teamStats.ties > 0? '-' + teamStats.ties : ''}</p>
              <p>Points Scored: {teamStats.pointsFor}</p>
              <p>Points Against: {teamStats.pointsAgainst}</p>
              <p>Point Differential: {teamStats.netPoints}</p></div>}
            </div>
            {divisionStandings && <div>
                <p>Division Standings</p>
                {Object.entries(divisionStandings).map(([teamName, teamData], index) => (
                    <p key={teamName}>
                        {index + 1}. {teamName} - {teamData.wins} - {teamData.losses} {teamData.ties > 0? '-' + teamData.ties : ''}
                    </p>
                ))}
            </div>}
          </div>
        </div>
      ) : (
        <p>Loading...</p>
      )}
    </Panel>
  );
};

export default TeamInfoPanel;
