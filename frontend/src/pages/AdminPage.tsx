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

  useEffect(() => {
    const getUserList = async () => {
      const users = await fetchAllUsers();
      setUserList(users);
    };

    getUserList();
  }, []);

  const handleSave = () => {
    // todo
  };

  return (
    <LoggedInLayout
      children={
        <div className="flex items-center align-middle flex-col h-screen">
          <AdminSelectEdit />
          <div className="text-white pt-5">
            <UserSelect userList={userList} onUserSelect={setSelectedUser} />
            <UserEdit username={selectedUser} onSave={handleSave} />
          </div>
        </div>
      }
    ></LoggedInLayout>
  );
};

export default AdminPage;
