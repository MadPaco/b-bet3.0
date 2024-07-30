import AchievementRow from "../components/common/AchievementRow";
import AchievementCard from "../components/common/AchievementCard";
import LoggedInLayout from "../components/layout/LoggedInLayout";
import AchievementEarnedOverview from "../components/common/AchievementEarnedOverview";
import { fetchAllAchievements, fetchHiddenAchievements } from "../utility/api";
import { useEffect, useState } from "react";


const AchievementsPage: React.FC = () => {
    const [achievements, setAchievements] = useState([]);
    const [isMobile, setIsMobile] = useState(false);
    const [hiddenAchievements, setHiddenAchievements] = useState([]);


    useEffect(() => {
        fetchAllAchievements().then((response) => {
            response.json().then((data) => {
                setAchievements(data);
            });
        });

        fetchHiddenAchievements().then((response) => {
            response.json().then((data) => {
                setHiddenAchievements(data);
            });
        }
        );

        const handleResize = () => {
            setIsMobile(window.innerWidth <= 768);
        };

        window.addEventListener("resize", handleResize);
        handleResize();

        return () => {
            window.removeEventListener("resize", handleResize);
        };
    }, []);

    return (
        <LoggedInLayout>
            <div className="container mx-auto p-4 text-white">
                <h1 className="text-3xl font-bold text-center">Achievements</h1>
                <AchievementEarnedOverview />
                <div className="flex">
                    <label className="text-center">
                        Search Achievements
                        <input type="text" placeholder="Search Achievements" className="w-full bg-gray-700 text-white p-2 rounded-lg my-2" />
                    </label>
                </div>


                <label >
                    Show earned
                    <input type="checkbox" placeholder="Show earned" className="w-full bg-gray-700 text-white p-2 rounded-lg my-2" />
                </label>

                <div className="text-center flex flex-col items-center gap-2">
                    {achievements.map((achievement: any) => (
                        isMobile ?
                            <AchievementCard key={achievement.id} achievement={achievement} isMobile={isMobile} />
                            :
                            <AchievementRow key={achievement.id} achievement={achievement} />
                    ))}
                </div>
                <div>
                    <h1 className="text-2xl font-bold text-center">Hidden Achievements</h1>
                    <div className="text-center flex flex-col items-center gap-2">
                        {hiddenAchievements.map((achievement: any) => (
                            isMobile ?
                                <AchievementCard key={achievement.id} achievement={achievement} isMobile={isMobile} />
                                :
                                <AchievementRow key={achievement.id} achievement={achievement} />
                        ))}
                    </div>
                </div>
            </div>
        </LoggedInLayout >
    );
}

export default AchievementsPage;