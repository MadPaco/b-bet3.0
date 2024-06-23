import { useAuth } from '../auth/AuthContext';
import { useEffect, useState } from 'react';
import nflTeams from '../../data/nflTeams';
import { useNavigate } from 'react-router';
import { updateUser } from '../../utility/api';
import { fetchUserInfo } from '../../utility/api';

const ProfileForm: React.FC = () => {
  const {
    username: initialUsername,
    favTeam: initialFavTeam,
    email: initialEmail,
    createdAt,
  } = useAuth();
  const navigate = useNavigate();
  const [usernameState, setUsername] = useState<string | null>(initialUsername);
  const [favTeamState, setFavTeam] = useState<string | null>(initialFavTeam);
  const [emailState, setEmail] = useState<string | null>(initialEmail);
  const [password, setPassword] = useState('');
  const [confirmPassword, setConfirmPassword] = useState('');
  const [bioState, setBio] = useState('');
  const [editMode, setEditMode] = useState(false);
  const [profilePicture, setProfilePicture] = useState<string | null>(null);
  const [profilePictureUrl, setProfilePictureUrl] = useState<string | null>(null);


  useEffect(() => {
    if (initialUsername) {
      fetchUserInfo(initialUsername)
        .then((data) => {
          if (data.profilePicture === null) {
            setProfilePicture('assets/images/defaultUser.webp');
          }
          else{
            setProfilePicture(data.profilePicture);
          }
          if (data.bio) {
            setBio(data.bio);
          }
        })
        .catch((error) => console.error(error));
    }
  }, [initialUsername]);

  useEffect(() => {
    if (profilePicture) {
      const fetchProfilePicture = async () => {        try {
          const response = await fetch(`http://127.0.0.1:8000/profile-picture/${profilePicture}`);
          if (response.ok) {
            setProfilePictureUrl(response.url);
          } else {
            setProfilePictureUrl('/assets/images/defaultUser.webp');
          }
        } catch (error) {
          console.error('Error fetching profile picture:', error);
          setProfilePictureUrl('/assets/images/defaultUser.webp');
        }
      };
      fetchProfilePicture();
    }
  }, [profilePicture]);

  const handleSubmit = async (event: React.FormEvent<HTMLFormElement>) => {
    event.preventDefault();
  
    if (password !== confirmPassword) {
      alert('Passwords do not match');
      return;
    }
  
    // Use FormData to handle file uploads
    const formData = new FormData();
  
    if (password !== '') {
      formData.append('password', password);
    }
  
    if (emailState !== '' && emailState !== initialEmail) {
      formData.append('email', emailState);
    }
    if (favTeamState !== '' && favTeamState !== initialFavTeam) {
      formData.append('favTeam', favTeamState);
    }
    if (usernameState !== '' && usernameState !== initialUsername) {
      formData.append('username', usernameState);
    }
    if (bioState !== '') {
      formData.append('bio', bioState);
    }
    if (profilePicture) {
      formData.append('profilePicture', profilePicture);
    }
  
    try {
      // Assuming updateUser is capable of handling FormData
      await updateUser(initialUsername, formData);
      navigate('/login');
    } catch (error) {
      console.error(error);
    }
  };

  const handleInputChange = (
    event: React.ChangeEvent<HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement>,
  ) => {
    const { name, value, files } = event.target;
    switch (name) {
      case 'username':
        setUsername(value);
        break;
      case 'favTeam':
        setFavTeam(value);
        break;
      case 'email':
        setEmail(value);
        break;
      case 'password':
        setPassword(value);
        break;
      case 'confirmPassword':
        setConfirmPassword(value);
        break;
      case 'bio':
        setBio(value);
        break;
      case 'profilePicture':
        if (files && files[0]) {
          setProfilePicture(files[0]);
        }
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
    <div className="bg-gray-800 py-8 px-4 sm:px-6 lg:px-8 flex items-center justify-center min-h-screen lg:min-h-[50vh]">
      <div className="max-w-md w-full space-y-8">
        <div className="text-center">
          <h2 className="mt-6 text-3xl font-extrabold text-white">Profile Page</h2>
          <p className="mt-2 text-sm text-gray-400">
            Member since: {createdAt ? createdAt.toLocaleDateString() : 'N/A'}
          </p>
        </div>
        <form onSubmit={handleSubmit} className="space-y-6 bg-white p-6 rounded-lg shadow-lg text-black">
          <div className="space-y-4">
            <div>
              <label htmlFor="username" className="sr-only">Username</label>
              <input
                id="username"
                name="username"
                type="text"
                onChange={handleInputChange}
                value={usernameState || ''}
                readOnly={!editMode}
                className="appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                placeholder="Username"
              />
            </div>
            <div>
              <label htmlFor="favTeam" className="sr-only">Favorite Team</label>
              <select
                id="favTeam"
                name="favTeam"
                onChange={handleInputChange}
                value={favTeamState || ''}
                disabled={!editMode}
                className={`appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm ${editMode ? '' : 'bg-gray-100 cursor-not-allowed'}`}
              >
                {nflTeams.map((team) => (
                  <option key={team} value={team}>{team}</option>
                ))}
              </select>
            </div>
            <div>
              <label htmlFor="email" className="sr-only">Email address</label>
              <input
                id="email"
                name="email"
                type="email"
                onChange={handleInputChange}
                value={emailState || ''}
                readOnly={!editMode}
                className="appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                placeholder="Email address"
              />
            </div>
            <div>
              <label htmlFor='bio' className='sr-only'>Bio</label>
              <input
                id='bio'
                name='bio'
                type='text'
                value={bioState || ''}
                onChange={handleInputChange}
                readOnly={!editMode}
                className='appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm'
                placeholder='Bio'
              />
            </div>
            <div>
                <label htmlFor='profilePicture'>Profile Picture:</label>
                <img src={profilePictureUrl ? profilePictureUrl : 'error'}></img>
                {editMode && (
                    <input
                    id='profilePicture'
                    name='profilePicture'
                    type='file'
                    accept='image/*'
                    onChange={handleInputChange}
                    className='appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm'
                    />
                )}
            </div>
            <div>
              <label htmlFor="password" className="sr-only">New Password</label>
              <input
                id="password"
                name="password"
                type="password"
                value={password}
                onChange={handleInputChange}
                readOnly={!editMode}
                className="appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                placeholder="New Password"
              />
            </div>
            {editMode && (
              <div>
                <label htmlFor="confirmPassword" className="sr-only">Confirm Password</label>
                <input
                  id="confirmPassword"
                  name="confirmPassword"
                  type="password"
                  value={confirmPassword}
                  onChange={handleInputChange}
                  className="appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                  placeholder="Confirm Password"
                />
              </div>
            )}
          </div>
          <div className="space-y-4">
            {editMode && (
              <button
                type="submit"
                className="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
              >
                Update (this will log you out)
              </button>
            )}
            <button
              type="button"
              onClick={() => setEditMode(!editMode)}
              className="group relative w-full flex justify-center py-2 px-4 mt-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              {editMode ? 'Cancel' : 'Edit Userinfo'}
            </button>
          </div>
        </form>
      </div>
    </div>
  );
};

export default ProfileForm;
