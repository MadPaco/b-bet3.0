import LoggedInLayout from '../components/layout/LoggedInLayout';
import { fetchAllUsers } from '../utility/api';
import { useState, useEffect } from 'react';
import Panel from '../components/common/Panel';
import {User} from '../utility/types';

const AllUsersPage: React.FC = () => {
  const [userList, setUserList] = useState([]);

  useEffect(() => {
    async function getUsers() {
      const users = await fetchAllUsers();
      setUserList(users);
    }

    getUsers();
  }, []);

  return (
    <LoggedInLayout>
      <div className="flex items-center align-mid flex-wrap">
        {userList.map((user: User) => {
          return (
            <div className="flex flex-1">
              <Panel
                children={
                  <div key={user.username}>
                    <p>{user.username}</p>
                    <p>{user.favTeam}</p>
                  </div>
                }
              />
            </div>
          );
        })}
        ;
      </div>
    </LoggedInLayout>
  );
};
export default AllUsersPage;
