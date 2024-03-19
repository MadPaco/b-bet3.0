import LoggedInLayout from '../components/layout/LoggedInLayout';
import TeamInfoPanel from '../components/Panels/TeamInfoPanel';

const ProfilePage: React.FC = () => {
  return (
    <LoggedInLayout>
      <div className="flex flex-col lg:pt-10 lg:grid lg:grid-cols-3 lg:grid-rows-3">
        <div className="lg:col-span-1 lg:row-span-1">
          <TeamInfoPanel />
        </div>
      </div>
    </LoggedInLayout>
  );
};
export default ProfilePage;
