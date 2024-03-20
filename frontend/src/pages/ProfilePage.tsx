// ProfilePage.tsx
import LoggedInLayout from '../components/layout/LoggedInLayout';
import { useAuth } from '../components/auth/AuthContext';
import { useState } from 'react';
import ProfileForm from '../components/form/ProfileForm';

const ProfilePage: React.FC = () => {
  const {
    username: initialUsername,
    favTeam: initialFavTeam,
    email: initialEmail,
    createdAt,
  } = useAuth();
  const [editMode, setEditMode] = useState(false);

  return (
    <LoggedInLayout>
      <div className="flex flex-col lg:pt-10 px-10 text-white align-middle items-center">
        <div>
          <ProfileForm
            initialUsername={initialUsername}
            initialFavTeam={initialFavTeam}
            initialEmail={initialEmail}
            createdAt={createdAt}
            editMode={editMode}
            setEditMode={setEditMode}
          />
        </div>
      </div>
    </LoggedInLayout>
  );
};

export default ProfilePage;
