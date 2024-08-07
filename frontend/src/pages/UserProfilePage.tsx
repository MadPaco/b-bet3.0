import { useEffect, useState } from 'react';
import { useParams } from 'react-router-dom';
import { fetchUserInfo, fetchShortUserStats, fetchTeamInfo, fetchHiddenCompletion, fetchNonHiddenCompletion } from '../utility/api';
import LoggedInLayout from '../components/layout/LoggedInLayout';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faTrophy, faCrosshairs, faCoins } from '@fortawesome/free-solid-svg-icons';

interface stats {
  totalPoints: number;
  currentPlace: number;
  hitRate: number;
  betsPlaced: number;
  highestScoringWeek: number;
  lowestScoringWeek: number;
}

interface hiddenCompletion {
  earned: number;
  total: number;
}

interface nonHiddenCompletion {
  earned: number;
  total: number;
}

const UserProfilePage: React.FC = () => {
  const { username } = useParams<{ username: string }>();
  const [user, setUser] = useState<any>(null);
  const [profilePicture, setProfilePicture] = useState<string | null>(null);
  const [profilePictureUrl, setProfilePictureUrl] = useState<string | null>(null);
  const [stats, setStats] = useState<stats | null>(null);
  const [nonHiddenCompletion, setNonHiddenCompletion] = useState<nonHiddenCompletion | null>(null);
  const [hiddenCompletion, setHiddenCompletion] = useState<hiddenCompletion | null>(null);
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

        const statsData = await fetchShortUserStats(username);
        setStats(statsData);

        const nonHiddenData = await fetchNonHiddenCompletion(username);
        setNonHiddenCompletion(nonHiddenData);

        const hiddenData = await fetchHiddenCompletion(username);
        setHiddenCompletion(hiddenData);
      } catch (error) {
        console.error('Error fetching user data:', error);
      }
    }

    if (username) {
      getUserData();
    }
  }, [username, location.pathname]);

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
      <div className='h-screen lg:flex lg:items-center lg:justify-center'>
        <div className="relative flex flex-col h-screen lg:h-4/5 text-white lg:w-1/4 lg:bg-gray-900 lg:p-4 lg:justify-center lg:align-middle lg:rounded-xl lg:border-2 lg:border-highlightCream">
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
              <p className="text-center text-highlightCream">{user.favTeam}</p>
              <p className="text-center mt-4">{user.bio}</p>
            </div>

            <div className='flex w-full bg-gray-700 pt-2 rounded-xl text-highlightGold'>
              <div className='flex w-1/3 flex-col items-center'>
                <FontAwesomeIcon icon={faTrophy} />
                <p className='text-center m-1 text-highlightCream'>Rank: {stats ? stats.currentPlace : ''}</p>
              </div>
              <div className='flex w-1/3 flex-col items-center'>
                <FontAwesomeIcon icon={faCoins} />
                <p className='text-center m-1 text-highlightCream'>Points: {stats ? stats.totalPoints : ''}</p>
              </div>
              <div className='flex w-1/3 flex-col items-center'>
                <FontAwesomeIcon icon={faCrosshairs} />
                <p className='text-center m-1 text-highlightCream'>HitRate: {stats ? (stats.hitRate * 100).toFixed(2) : ''}%</p>
              </div>
            </div>

            {/* Profile picture container */}
            <div className="absolute top-1/3 transform -translate-y-1/3 flex justify-center w-full"
            >
              <img
                src={profilePictureUrl || '/assets/images/defaultUser.webp'}
                alt={`${user.username}'s profile`}
                className="w-32 h-32 rounded-full border-4 border-highlightCream"
              />
            </div>
          </div>
          <div className="flex flex-col items-center bg-gray-700 text-white h-2/3 pt-24">
            <div className='w-full flex justify-evenly align-top mx-2 mb-4 font-bold text-highlightGold'>
              <h1 className='w-1/2 text-center'>Stats</h1>
              <h1 className='w-1/2 text-center'>Achievements</h1>
            </div>
            <div className="flex w-full text-highlightCream">
              <div className='w-full flex items-center align-top mx-2'>
                {stats && (<div className='w-1/2 text-center'>

                  <p>Bets placed: {stats.betsPlaced}</p>
                  <p>Best week: {stats.highestScoringWeek}</p>
                  <p>Worst week: {stats.lowestScoringWeek}</p>
                </div>)}
                {hiddenCompletion && nonHiddenCompletion && (
                  <div className='w-1/2  text-center'>

                    <p>Non-Hidden: {nonHiddenCompletion.earned}/{nonHiddenCompletion.total}</p>
                    <p>Hidden: {hiddenCompletion.earned}/{hiddenCompletion.total}</p>
                  </div>)}
              </div>
            </div>
            <div className='w-full flex'>
              <div className='w-1/2 flex flex-col items-center h-full align-top mx-2'>
                <a href={`/users/${username}/stats`} className="text-center w-full m-2 mb-2 bg-highlightCream text-black rounded-xl">All Stats</a>
              </div>
              <div className='w-1/2 flex flex-col items-center h-full align-top mx-2'>
                <a href={`/users/${username}/achievements`} className="text-center w-full m-2 mb-2 bg-highlightCream text-black rounded-xl">Achievements</a>
              </div>
            </div>
            <div className='w-full h-full flex items-center align-middle justify-center'>
              <a href='/users/all' className="text-center w-1/2 m-2 mb-2 bg-highlightCream text-black rounded-xl">All Profiles</a>
            </div>
          </div>
        </div>
      </div>
    </LoggedInLayout >
  );
};

export default UserProfilePage;
