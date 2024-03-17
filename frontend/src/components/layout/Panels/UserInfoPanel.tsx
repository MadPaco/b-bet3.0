import Panel from '../../common/Panel';
import { useAuth } from '../../auth/AuthContext';

interface UserInfoPanelProps {
  color: string;
}

const UserInfoPanel: React.FC<UserInfoPanelProps> = ({ color }) => {
  const { username, createdAt } = useAuth();

  return (
    <Panel color={color}>
      {username ? (
        <div className="flex items-center text-gray-200">
          <img
            src={`/assets/images/defaultUser.png`}
            alt="default userpic"
            className="w-24 h-24 object-contain mr-4"
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
