import { useEffect, useState } from 'react';
import Panel from '../common/Panel';
import { useAuth } from '../auth/AuthContext';
import { fetchTeamInfo } from '../../utility/api';

interface TeamInfo {
  name: string;
  division: string;
  conference: string;
  logo: string;
  primaryColor: string;
}

interface TeamInfoPanelProps {
  color: string;
}

const TeamInfoPanel: React.FC<TeamInfoPanelProps> = ({ color }) => {
  const [teamInfo, setTeamInfo] = useState<TeamInfo | null>(null);
  const { favTeam } = useAuth();

  useEffect(() => {
    if (favTeam) {
      fetchTeamInfo(favTeam)
        .then((data) => setTeamInfo(data))
        .catch((error) => console.error(error));
    }
  }, [favTeam]);

  return (
    <Panel color={color}>
      {teamInfo ? (
        <div className="flex items-center text-gray-200">
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
              <p>Wins - Losses: 17-0</p> {/* This is a placeholder */}
              <p>Place in Division: 1</p> {/* This is a placeholder */}
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
