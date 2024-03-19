import Panel from '../common/Panel';
import { useAuth } from '../auth/AuthContext';

interface MessageOverviewPanelProps {
  color: string;
}

const MessageOverviewPanel: React.FC<MessageOverviewPanelProps> = () => {
  const { username } = useAuth();

  return (
    <Panel>
      {username ? (
        <div className="flex items-center text-gray-200">
          <img
            src={`/assets/images/defaultUser.png`}
            alt="default userpic"
            className="w-24 h-24 object-contain mr-4"
          />
          <div className="flex items-center">
            <div>
              <p>New Messages for {username} : 0</p>
            </div>
          </div>
        </div>
      ) : (
        <p>Loading...</p>
      )}
    </Panel>
  );
};

export default MessageOverviewPanel;
