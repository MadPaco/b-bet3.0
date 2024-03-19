import TeamInfoPanel from '../components/Panels/TeamInfoPanel';
import UserInfoPanel from '../components/Panels/UserInfoPanel';
import MessageOverviewPanel from '../components/Panels/MessageOverviewPanel';
import ChatPanel from '../components/Panels/ChatPanel';
import ActivityPanel from '../components/Panels/ActivityPanel';
import NewsPanel from '../components/Panels/NewsPanel';
import LoggedInLayout from '../components/layout/LoggedInLayout';
import '../utility/api';

const Dashboard: React.FC = () => {
  return (
    <LoggedInLayout>
      <div className="flex flex-col lg:pt-10 lg:grid lg:grid-cols-3 lg:grid-rows-3">
        <div className="lg:col-span-1 lg:row-span-1">
          <TeamInfoPanel />
        </div>
        <div className="lg:col-span-1 lg:row-span-1">
          <NewsPanel />
        </div>
        <div className="lg:col-span-1 lg:row-span-1">
          <MessageOverviewPanel />
        </div>
        <div className="lg:col-span-2 lg:row-span-2">
          <ChatPanel />
        </div>
        <div className="lg:col-span-1 lg:row-span-1">
          <UserInfoPanel />
        </div>
        <div className="lg:col-span-1 lg:row-span-1">
          <ActivityPanel />
        </div>
      </div>
    </LoggedInLayout>
  );
};

export default Dashboard;
