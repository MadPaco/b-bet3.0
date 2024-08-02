import React, { useEffect, useState } from 'react';
import { useParams } from 'react-router-dom';
import { fetchUserInfo, fetchShortUserStats, fetchTeamInfo } from '../utility/api';
import LoggedInLayout from '../components/layout/LoggedInLayout';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faTrophy, faCrosshairs, faCoins } from '@fortawesome/free-solid-svg-icons';
import { color } from 'chart.js/helpers';

interface stats {
  totalPoints: number;
  currentPlace: number;
  hitRate: number;
}

const UserProfilePage: React.FC = () => {
  const { username } = useParams<{ username: string }>();
  const [user, setUser] = useState<any>(null);
  const [profilePicture, setProfilePicture] = useState<string | null>(null);
  const [profilePictureUrl, setProfilePictureUrl] = useState<string | null>(null);
  const [stats, setStats] = useState<stats | null>(null);
  const [teamInfo, setTeamInfo] = useState<any>(null);

  useEffect(() => {
    if (profilePicture) {
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

  useEffect(() => {
    async function getUserData() {
      try {
        const userData = await fetchUserInfo(username);
        setUser(userData);
        setProfilePicture(userData.profilePicture);

        const statsResponse = await fetchShortUserStats(username);
        if (statsResponse.ok) {
          const statsData = await statsResponse.json();
          setStats(statsData);
        } else {
          console.error('Error fetching user stats:', statsResponse.statusText);
        }
      } catch (error) {
        console.error('Error fetching user data:', error);
      }
    }

    if (username) {
      getUserData();
    }
  }, [username]);

  useEffect(() => {
    async function getTeamData(teamName: string) {
      try {
        const teamData = await fetchTeamInfo(teamName);
        setTeamInfo(teamData);
      } catch (error) {
        console.error('Error fetching team data:', error);
      }
    }

    if (user && user.favTeam) {
      getTeamData(user.favTeam);
    }
  }, [user]);

  if (!user) {
    return <div>Loading...</div>;
  }

  return (
    <LoggedInLayout>
      <div className="relative flex flex-col h-screen text-white">
        <div
          className="p-3 h-1/3 flex flex-col items-center justify-center"
          style={{
            backgroundImage: teamInfo ? `url(/assets/images/teams/${teamInfo.logo})` : 'none',
            backgroundSize: 'cover',
            backgroundPosition: 'center'
          }}
        >
          <div className='bg-gray-700 w-full bg-opacity-90 rounded-xl mb-2'>
            <h2 className="text-2xl font-bold text-center mb-2 text-highlightGold">{user.username}</h2>
            <p className="text-center">{user.favTeam}</p>
            <p className="text-center mt-4">{user.bio}</p>
          </div>

          <div className='flex w-full bg-gray-700 pt-2 rounded-xl'>
            <div className='flex w-1/3 flex-col items-center'>
              <FontAwesomeIcon icon={faTrophy} />
              <p className='text-center m-1'>Rank: {stats ? stats.currentPlace : ''}</p>
            </div>
            <div className='flex w-1/3 flex-col items-center'>
              <FontAwesomeIcon icon={faCoins} />
              <p className='text-center m-1'>Points: {stats ? stats.totalPoints : ''}</p>
            </div>
            <div className='flex w-1/3 flex-col items-center'>
              <FontAwesomeIcon icon={faCrosshairs} />
              <p className='text-center m-1'>HitRate: {stats ? (stats.hitRate * 100).toFixed(2) : ''}%</p>
            </div>
          </div>

          {/* Profile picture container */}
          <div className="absolute top-1/3 transform -translate-y-1/3 flex justify-center w-full"
          >
            <img
              src={profilePictureUrl || '/assets/images/defaultUser.webp'}
              alt={`${user.username}'s profile`}
              className="w-32 h-32 rounded-full border-4 border-white"
            />
          </div>
        </div>
        <div className="flex flex-col items-center justify-center bg-gray-700 text-white h-2/3 pt-16">

          <div className="flex flex-col mt-4">
            <a href={`/users/${username}/stats`} className="text-center mb-2">Stats</a>
            <a href={`/users/${username}/achievements`} className="text-center">Achievements</a>
          </div>
        </div>
      </div>
    </LoggedInLayout >
  );
};

export default UserProfilePage;
