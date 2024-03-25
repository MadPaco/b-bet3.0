import { useEffect, useState } from 'react';
import { fetchUserInfo, fetchAllTeamNames } from '../../utility/api';
interface UserEditProps {
  username: string;
  onSave: () => void;
}

const UserEdit: React.FC<UserEditProps> = ({ username, onSave }) => {
  const [favTeam, setFavTeam] = useState('');
  const [usernameState, setUsernameState] = useState(username || '');
  const [email, setEmail] = useState('');
  const [roles, setRoles] = useState<string[]>([]);
  const [teamNameList, setTeamNameList] = useState<string[]>([]);

  useEffect(() => {
    if (username != '') {
      fetchUserInfo(username).then((userInfo) => {
        if (userInfo) {
          setUsernameState(userInfo.username);
          setFavTeam(userInfo.favTeam);
          setEmail(userInfo.email);
          setRoles(userInfo.roles);
        }
      });
    }
  }, [username]);

  useEffect(() => {
    const getTeamNames = async () => {
      const teamNames = await fetchAllTeamNames();
      setTeamNameList(teamNames);
    };
    getTeamNames();
  }, []);

  return (
    <div>
      {username ? (
        <div>
          <label>
            Username:
            <input
              type="text"
              className="text-black"
              value={usernameState}
              onChange={(e) => setUsernameState(e.target.value)}
            />
          </label>
          <label>
            Favorite Team:
            <select className="text-black" value={favTeam}>
              {teamNameList.map((team: string) => {
                return (
                  <option key={team} value={team}>
                    {team}
                  </option>
                );
              })}
            </select>
          </label>
          <label>
            Email:
            <input
              type="text"
              className="text-black"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
            />
          </label>
          <label>
            Roles:
            <input
              type="text"
              className="text-black"
              value={roles.join(', ')}
              onChange={(e) =>
                setRoles(
                  e.target.value.split(', ').filter((role) => role != ''),
                )
              }
            />
          </label>
          <label></label>
          <button onClick={() => onSave()}>Save</button>
        </div>
      ) : (
        <p>No user selected</p>
      )}
    </div>
  );
};

export default UserEdit;
