import Panel from '../common/Panel';
import { useAuth } from '../auth/AuthContext';

interface ActivityPanelProps { }

const ActivityPanel: React.FC<ActivityPanelProps> = () => {
  const { username } = useAuth();

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
              <p>Recent Activity: </p>
              <ul>
                <li>Placeholder activity 1</li>
                <li>Placeholder activity 2</li>
              </ul>
            </div>
          </div>
        </div>
      ) : (
        <p>Loading...</p>
      )}
    </Panel>
  );
};

export default ActivityPanel;
