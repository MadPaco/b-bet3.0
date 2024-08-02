// ProfilePage.tsx
import LoggedInLayout from '../components/layout/LoggedInLayout';
import ProfileForm from '../components/form/ProfileForm';

const ProfilePage: React.FC = () => {
  return (
    <LoggedInLayout>
      <div className="flex flex-col lg:pt-10 px-10 text-white align-middle items-center">
        <div>
          <ProfileForm />
        </div>
      </div>
    </LoggedInLayout>
  );
};

export default ProfilePage;
