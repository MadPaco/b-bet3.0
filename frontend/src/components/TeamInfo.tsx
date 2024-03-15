import React from 'react';

interface TeamInfoProps {
  teamInfo: {
    logo: string;
    name: string;
    shorthand_name: string;
    location: string;
    division: string;
    conference: string;
  } | null;
}

const TeamInfo: React.FC<TeamInfoProps> = ({ teamInfo }) => {
  if (!teamInfo) {
    return null;
  }

  return (
    <div>
      <img src={`/assets/images/teams/${teamInfo.logo}`} alt={teamInfo.name} />
      <h2>{teamInfo.name}</h2>
    </div>
  );
};

export default TeamInfo;
