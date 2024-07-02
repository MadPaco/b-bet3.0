import LoggedInLayout from "../components/layout/LoggedInLayout";
import { fetchAllAchievements } from "../utility/api";
import { useEffect, useState } from "react";

const AchievementsPage: React.FC = () => {
    const [achievements, setAchievements] = useState([]);
    useEffect(() => {
        fetchAllAchievements().then((response) => {
            response.json().then((data) => {
                setAchievements(data);
            });
        });
    }, []);

    return (
        <LoggedInLayout>
            <div className="container mx-auto p-4">
                <div className="text-center grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    {achievements.map((achievement: any) => (
                        <div key={achievement.id} className="bg-gray-700 text-white shadow-md rounded-lg p-4">
                            <h1 className="text-xl font-bold mb-2">{achievement.name}</h1>
                            <img className="w-full h-2/3 object-cover mb-2 rounded-lg" src={`/assets/images/achievements/${achievement.image}`} alt={achievement.name} />
                            <p className="mb-2">{achievement.description}</p>
                            <p className="mb-3 italic font-bold">{achievement.flavorText}</p>
                            <p className="text-xs">{achievement.category}</p>
                        </div>
                    ))}
                </div>
            </div>
        </LoggedInLayout>
    );
}

export default AchievementsPage;