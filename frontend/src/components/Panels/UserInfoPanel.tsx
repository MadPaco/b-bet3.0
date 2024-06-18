import Panel from '../common/Panel';
import { useAuth } from '../auth/AuthContext';

interface UserInfoPanelProps {}

const UserInfoPanel: React.FC<UserInfoPanelProps> = () => {
  const { username, createdAt } = useAuth();

  return (
    <Panel>
      {username ? (
        <div className="flex items-center">
          <img
            src={`/assets/images/defaultUser.webp`}
            alt="default userpic"
            className="w-24 h-24 object-contain mr-4 rounded-full"
          />
          <div className="flex items-center">
            <div>
              <p>User: {username}</p>
              <p>Member since: {createdAt?.toString().split('GMT')[0]}</p>
            </div>
          </div>
        </div>
      ) : (
        <p>Loading...</p>
      )}
    </Panel>
  );
};

export default UserInfoPanel;
