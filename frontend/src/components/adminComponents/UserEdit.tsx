import { useEffect, useState } from 'react';
import {
  fetchUserInfo,
  fetchAllTeamNames,
  updateUser,
} from '../../utility/api';
interface UserEditProps {
  username: string;
}

interface User {
  username: string;
  favTeam: string;
  email: string;
  roles: string[];
}

const UserEdit: React.FC<UserEditProps> = ({ username }) => {
  const [initalUser, setInitialUser] = useState({} as User);
  const [favTeam, setFavTeam] = useState('');
  const [usernameState, setUsernameState] = useState(username || '');
  const [email, setEmail] = useState('');
  const [roles, setRoles] = useState<string[]>([]);
  const [teamNameList, setTeamNameList] = useState<string[]>([]);
  const [password, setPassword] = useState('');
  const [confirmPassword, setConfirmPassword] = useState('');

  const saveChanges = async () => {
    const formData = new FormData();

    if (usernameState !== initalUser.username) {
      formData.append('username', usernameState);
    }
    if (favTeam !== initalUser.favTeam) {
      formData.append('favTeam', favTeam);
    }
    if (email !== initalUser.email) {
      formData.append('email', email);
    }
    if (roles !== initalUser.roles) {
      roles.forEach(role => formData.append('roles[]', role));
    }
    if (password !== '' && password === confirmPassword) {
      formData.append('password', password);
    }
    await updateUser(username, formData);
  };

  useEffect(() => {
    if (username != '') {
      fetchUserInfo(username).then((userInfo) => {
        if (userInfo) {
          setInitialUser(userInfo);
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
        <div className="flex flex-col flex-1">
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
            <select
              className="text-black"
              value={favTeam}
              onChange={(e) => {
                setFavTeam(e.target.value);
              }}
            >
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
            <div>
              <label>
                <input
                  type="checkbox"
                  checked={roles.includes('ADMIN')}
                  onChange={(e) => {
                    if (e.target.checked) {
                      setRoles([...roles, 'ADMIN']);
                    } else {
                      setRoles(roles.filter((role) => role !== 'ADMIN'));
                    }
                  }}
                />
                Admin
              </label>
              <label>
                <input
                  type="checkbox"
                  checked={roles.includes('USER')}
                  onChange={(e) => {
                    if (e.target.checked) {
                      setRoles([...roles, 'USER']);
                    } else {
                      setRoles(roles.filter((role) => role !== 'USER'));
                    }
                  }}
                />
                User
              </label>
            </div>
          </label>
          <div>
            {' '}
            <label>
              Password:
              <input
                className="text-black"
                onChange={(e) => {
                  setPassword(e.target.value);
                }}
              ></input>
            </label>
            <label>
              Confirm Password:
              <input
                className="text-black"
                onChange={(e) => {
                  setConfirmPassword(e.target.value);
                }}
              ></input>
            </label>
          </div>

          <button onClick={() => saveChanges()}>Save</button>
        </div>
      ) : (
        <p>No user selected</p>
      )}
    </div>
  );
};

export default UserEdit;
