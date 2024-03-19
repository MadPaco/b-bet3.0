import Layout from '../components/layout/Layout';
import Sidebar from '../components/layout/Sidebar';

const AdminPage: React.FC = () => {
  return (
    <Layout
      content={
        <div className="flex flex-col lg:grid lg:grid-cols-7 w-full">
          <Sidebar color={'black'} />
          <div className="grid col-span-6 ">
            <div className="flex flex-col lg:pt-10 lg:grid lg:grid-cols-3 lg:grid-rows-3">
              <div className="lg:col-span-1 lg:row-span-1">
                <h1>Admin Panel</h1>
              </div>
            </div>
          </div>
        </div>
      }
    />
  );
};
export default AdminPage;
