import Panel from '../common/Panel';
import { useState, useEffect } from 'react';
import { fetchDivisionStandings, fetchTeamInfo } from '../../utility/api';
import { useAuth } from '../auth/AuthContext';
import { TeamInfo } from '../../utility/types';

const NewsPanel: React.FC<DivisionPanelProps> = () => {
  const { favTeam } = useAuth();
  const [divisionStandings, setDivisionStandings] = useState<TeamInfo[] | null>(null);
  const [teamInfo, setTeamInfo] = useState<TeamInfo | null>(null);

  useEffect(() => {
    if (favTeam) {
      fetchTeamInfo(favTeam)
        .then((data) => setTeamInfo(data))
        .catch((error) => console.error(error));
    }
  }, [favTeam]);

  useEffect(() => {
    if (teamInfo) {
      fetchDivisionStandings(teamInfo.conference, teamInfo.division)
        .then((data) => {
          console.log(data);  
          setDivisionStandings(data);
        })
        .catch((error) => console.error(error));
    }
  }, [teamInfo]);

  return (
    <Panel >
      <div className="flex flex-col h-full">
        <div className="mb-4">
          <h3 className="text-xl font-semibold mb-2 text-center">Division Standings</h3>
          {divisionStandings ? (
            <div className="space-y-2">
              {divisionStandings.map((team, index) => (
                <div
                  key={team.name}
                  className={`flex items-center justify-between p-2 rounded-lg ${
                    index % 2 === 0 ? 'bg-gray-500' : 'bg-gray-700'
                  }`}
                >
                  <span className="font-medium">
                    {index + 1}. {team.name}
                  </span>
                  <span className="text-sm text-white">
                    {team.wins} - {team.losses} {team.ties > 0 ? `- ${team.ties}` : ''}
                  </span>
                </div>
              ))}
            </div>
          ) : (
            <p className="text-gray-500">Loading division standings...</p>
          )}
        </div>
      </div>
    </Panel>
  );
};

export default NewsPanel;
