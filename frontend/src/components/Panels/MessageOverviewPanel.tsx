import Panel from '../common/Panel';
import { useAuth } from '../auth/AuthContext';

interface MessageOverviewPanelProps {}

const MessageOverviewPanel: React.FC<MessageOverviewPanelProps> = () => {
  const { username } = useAuth();

  return (
    <Panel>
      {username ? (
        <div className="flex items-center flex-col">
          <h2 className='text-xl font-semibold mb-2' >Messages</h2>
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
