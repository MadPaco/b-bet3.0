import { useEffect, useState } from 'react';
import LoggedInLayout from '../components/layout/LoggedInLayout';
import { fetchAllUsers } from '../utility/api';
import UserSelect from '../components/adminComponents/UserSelect';
import UserEdit from '../components/adminComponents/UserEdit';
import AdminSelectEdit from '../components/adminComponents/AdminSelectEdit';

interface User {
  username: string;
  favTeam: string;
}

const AdminPage: React.FC = () => {
  const [userList, setUserList] = useState<User[]>([]);
  const [selectedUser, setSelectedUser] = useState<string>('');
  const [selectedButton, setSelectedButton] = useState<string>('');

  useEffect(() => {
    const getUserList = async () => {
      const users = await fetchAllUsers();
      setUserList(users);
    };

    getUserList();
  }, []);

  const handleButtonSelect = (button: string) => {
    setSelectedButton(button);
  };

  return (
    <LoggedInLayout
      children={
        <div className="flex items-center align-middle flex-col h-screen">
          <AdminSelectEdit onButtonSelect={handleButtonSelect} />
          <div className="text-white pt-5">
            {selectedButton === 'Edit User' && (
              <div>
                {' '}
                <UserSelect
                  userList={userList}
                  onUserSelect={setSelectedUser}
                />
                <UserEdit username={selectedUser} />
              </div>
            )}
          </div>
        </div>
      }
    ></LoggedInLayout>
  );
};

export default AdminPage;
