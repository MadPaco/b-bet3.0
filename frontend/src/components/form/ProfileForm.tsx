import { useAuth } from '../auth/AuthContext';
import { useEffect, useState } from 'react';
import nflTeams from '../../data/nflTeams';

const ProfileForm: React.FC = () => {
  const {
    username: initialUsername,
    favTeam: initialFavTeam,
    email: initialEmail,
    createdAt,
  } = useAuth();

  const [usernameState, setUsername] = useState({ initialUsername });
  const [favTeamState, setFavTeam] = useState({ initialFavTeam });
  const [emailState, setEmail] = useState({ initialEmail });
  const [password, setPassword] = useState('');
  const [confirmPassword, setConfirmPassword] = useState('');
  const [editMode, setEditMode] = useState(false);

  const handleSubmit = (event) => {
    event.preventDefault();
    // handle submit
  };

  const handleInputChange = (event) => {
    switch (event.target.name) {
      case 'username':
        setUsername(event.target.value);
        break;
      case 'favTeam':
        setFavTeam(event.target.value);
        break;
      case 'email':
        setEmail(event.target.value);
        break;
      case 'password':
        setPassword(event.target.value);
        break;
      case 'confirmPassword':
        setConfirmPassword(event.target.value);
        break;
      default:
        break;
    }
  };

  useEffect(() => {
    if (!editMode) {
      setUsername(initialUsername);
      setFavTeam(initialFavTeam);
      setEmail(initialEmail);
    }
  }, [editMode, initialUsername, initialFavTeam, initialEmail]);

  return (
    <form
      onSubmit={handleSubmit}
      className=" bg-black text-white space-y-4 p-3"
    >
      <p className="text-lg font-bold">
        {' '}
        Member since: {createdAt.toLocaleDateString()}
      </p>
      <label className="block">
        <span className="text-gray-700">Username:</span>
        <input
          type="text"
          name="username"
          onChange={handleInputChange}
          value={usernameState}
          readOnly={!editMode}
          className="mt-1  text-black px-2 block w-full rounded-md border-gray-300 shadow-sm"
        />
      </label>
      <label className="block">
        <span className="text-gray-700 ">Favorite Team:</span>
        <select
          name="favTeam"
          onChange={handleInputChange}
          value={favTeamState}
          //opacity is a workaround to prevent the default behavious of disabled to darken the field
          className={`text-black ${editMode ? '' : 'opacity-100 cursor-not-allowed'}`}
          disabled={!editMode}
        >
          {nflTeams.map((team) => {
            return <option key={team}>{team}</option>;
          })}
        </select>
      </label>
      <label className="block">
        <span className="text-gray-700">Email:</span>
        <input
          type="email"
          name="email"
          onChange={handleInputChange}
          value={emailState}
          readOnly={!editMode}
          className="mt-1 text-black px-2 block w-full rounded-md border-gray-300 shadow-sm"
        />
      </label>
      <label className={editMode ? 'block' : 'hidden'}>
        <span className="text-gray-700">New Password:</span>
        <input
          type="password"
          name="password"
          value={password}
          onChange={handleInputChange}
          className="mt-1 text-black px-2 block w-full rounded-md border-gray-300 shadow-sm"
        />
      </label>
      <label className={editMode ? 'block' : 'hidden'}>
        <span className="text-gray-700">Confirm New Password:</span>
        <input
          type="password"
          name="confirmPassword"
          value={confirmPassword}
          onChange={handleInputChange}
          className="mt-1 text-black block w-full rounded-md border-gray-300 shadow-sm"
        />
      </label>
      <button
        type="submit"
        className={` ${editMode ? 'block' : 'hidden'} w-full py-2 px-4 text-white bg-blue-500 hover:bg-blue-700 rounded`}
      >
        Update
      </button>
      <button
        onClick={() => setEditMode(!editMode)}
        type="button"
        className={` w-full py-2 px-4 text-white bg-blue-500 hover:bg-blue-700 rounded`}
      >
        {editMode ? 'Cancel' : 'Edit Userinfo'}
      </button>
    </form>
  );
};

export default ProfileForm;
