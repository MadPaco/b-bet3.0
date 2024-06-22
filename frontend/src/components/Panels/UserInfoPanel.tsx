import { useEffect, useState } from 'react';
import Panel from '../common/Panel';
import { useAuth } from '../auth/AuthContext';
import { fetchUserInfo } from '../../utility/api';

interface UserInfoPanelProps {}

const UserInfoPanel: React.FC<UserInfoPanelProps> = () => {
  const { username, createdAt } = useAuth();
  const [profilePicture, setProfilePicture] = useState<string | null>(null);
  const [profilePictureUrl, setProfilePictureUrl] = useState<string | null>(null);

  useEffect(() => {
    if (username) {
      fetchUserInfo(username)
        .then((data) => {
          setProfilePicture(data.profilePicture);
        })
        .catch((error) => console.error(error));
    }
  }, [username]);

  useEffect(() => {
    if (profilePicture) {
      console.log(profilePicture);
      const fetchProfilePicture = async () => {
        try {
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

  return (
    <Panel>
      {username ? (
        <div className="flex items-center">
          <img
            {...(profilePictureUrl ? { src: profilePictureUrl } : { src: '/assets/images/defaultUser.webp' })}
            alt="Profile"
            className="w-24 h-24 object-contain mr-4 rounded-full"
          />
          <div>
            <p>User: {username}</p>
            <p>Member since: {createdAt?.toLocaleDateString()}</p>
          </div>
        </div>
      ) : (
        <p>Loading...</p>
      )}
    </Panel>
  );
};

export default UserInfoPanel;
