import React, { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import LoggedInLayout from '../components/layout/LoggedInLayout';
import { fetchAllUsers } from '../utility/api';
import Panel from '../components/common/Panel';
import { User } from '../utility/types';

const AllUsersPage: React.FC = () => {
  const [userList, setUserList] = useState<User[]>([]);
  const [profilePictures, setProfilePictures] = useState<{ [key: string]: string }>({});
  const navigate = useNavigate();

  useEffect(() => {
    async function getUsers() {
      const users = await fetchAllUsers();
      if (users) {
        setUserList(users);
        await fetchProfilePictures(users);
      }
    }

    getUsers();
  }, []);

  const fetchProfilePictures = async (users: User[]) => {
    const profilePicturesMap: { [key: string]: string } = {};

    await Promise.all(
      users.map(async (user) => {
        try {
          const response = await fetch(`http://127.0.0.1:8000/profile-picture/${user.profilePicture}`);
          if (response.ok) {
            profilePicturesMap[user.username] = response.url;
          } else {
            profilePicturesMap[user.username] = '/assets/images/defaultUser.webp';
          }
        } catch (error) {
          console.error('Error fetching profile picture for', user.username, error);
          profilePicturesMap[user.username] = '/assets/images/defaultUser.webp';
        }
      })
    );

    setProfilePictures(profilePicturesMap);
  };

  const handleUserClick = (username: string) => {
    navigate(`/users/${username}/profile`);
  };

  return (
    <LoggedInLayout>
      <div className="flex p-4 h-screen flex-wrap">
        {userList.map((user) => (
          <div
            className="bg-gray-900 rounded-lg shadow-lg p-4 cursor-pointer hover:bg-gray-500 w-full lg:w-1/5 lg:h-1/5 m-2 border-2 border-highlightCream"
            onClick={() => handleUserClick(user.username)}
          >
            <img
              src={profilePictures[user.username] || 'assets/images/defaultUser.webp'}
              alt={`${user.username}'s profile`}
              className="w-24 h-24 rounded-full mx-auto mb-4 border-2 border-highlightCream"
            />
            <div className="text-center">
              <h3 className="text-xl text-highlightGold font-bold mb-2">{user.username}</h3>
              <p className="text-highlightCream mb-2">{user.favTeam}</p>
              <p className="text-highlightCream text-sm">{user.bio}</p>
            </div>
          </div>
        ))}
      </div>
    </LoggedInLayout>
  );
};

export default AllUsersPage;