import LoggedInLayout from '../components/layout/LoggedInLayout';
import Panel from '../components/common/Panel';
import { useAuth } from '../components/auth/AuthContext';
import { useState } from 'react';
import { fetchAllUsers } from '../utility/api';

const handleSubmit = () => {
  // handle submit
};

const handleInputChange = () => {
  // handle input change
};

async function getUsers() {
  const userList = await fetchAllUsers();
  return userList;
}

const ProfilePage: React.FC = () => {
  const { username, favTeam, email, createdAt, roles } = useAuth();

  return (
    <LoggedInLayout>
      <div className="flex flex-col lg:pt-10">
        <Panel
          children={
            <div>
              {username}
              {favTeam}
              {email}
              {createdAt?.toString()}
              {roles}
            </div>
          }
        />
      </div>
    </LoggedInLayout>
  );
};

{
  /*
<form onSubmit={handleSubmit}>
              <label>
                Username:
                <input
                  type="text"
                  name="username"
                  value={username}
                  onChange={handleInputChange}
                />
              </label>
              <label>
                Email:
                <input
                  type="email"
                  name="email"
                  value={user.email}
                  onChange={handleInputChange}
                />
              </label>
              <button type="submit">Update</button>
            </form> */
}
export default ProfilePage;
