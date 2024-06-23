import React, { useEffect, useState } from 'react';
import { useParams } from 'react-router-dom';
import { fetchUserInfo } from '../utility/api'; 
import LoggedInLayout from '../components/layout/LoggedInLayout';
import Panel from '../components/common/Panel';

const UserProfilePage: React.FC = () => {
    const { username } = useParams<{ username: string }>(); 
    const [user, setUser] = useState<any>(null);
    const [profilePicture, setProfilePicture] = useState<string | null>(null);
    const [profilePictureUrl, setProfilePictureUrl] = useState<string | null>(null);

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

  useEffect(() => {
    async function getUser() {
      const userData = await fetchUserInfo(username);
      setUser(userData);
      setProfilePicture(userData.profilePicture);
    }

    getUser();
  }, [username]);

  if (!user) {
    return <div>Loading...</div>;
  }

  return (
    <LoggedInLayout>
        <Panel>
            <div className="rounded-lg bg-gray-500 shadow-lg p-6 m-3">
                <img
                src={profilePictureUrl || '/assets/images/defaultUser.webp'}
                alt={`${user.username}'s profile`}
                className="w-32 h-32 rounded-full mx-auto mb-4"
                />
                <h2 className="text-2xl font-bold text-center mb-2">{user.username}</h2>
                <p className="text-center text-gray-600">{user.favTeam}</p>
                <p className="text-center text-gray-700 mt-4">{user.bio}</p>
            </div>
        </Panel>

    </LoggedInLayout>
  );
};

export default UserProfilePage;
