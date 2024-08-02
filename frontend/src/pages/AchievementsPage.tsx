import AchievementRow from "../components/common/AchievementRow";
import AchievementCard from "../components/common/AchievementCard";
import LoggedInLayout from "../components/layout/LoggedInLayout";
import AchievementEarnedOverview from "../components/common/AchievementEarnedOverview";
import { fetchAllAchievements, fetchHiddenAchievements } from "../utility/api";
import { useEffect, useState } from "react";
import AchievementFilters from "../components/common/AchievementFilters";
import { useParams } from "react-router-dom";

const AchievementsPage: React.FC = () => {
    const { username } = useParams<{ username: string }>();
    const [achievements, setAchievements] = useState([]);
    const [isMobile, setIsMobile] = useState(false);
    const [searchQuery, setSearchQuery] = useState("");
    const [filters, setFilters] = useState({
        showEarned: true,
        showNotEarned: true,
        showHidden: true,
        showNonHidden: true,
        showAmount: true,
        showTiming: true,
        showWeekly: true,
        showStreaks: true,
        showSpecial: true,
        showMocking: true,
    });

    useEffect(() => {
        Promise.all([fetchAllAchievements(username), fetchHiddenAchievements(username)]).then(([allResponse, hiddenResponse]) => {
            Promise.all([allResponse.json(), hiddenResponse.json()]).then(([allData, hiddenData]) => {
                setAchievements([...allData, ...hiddenData]);
            });
        });

        const handleResize = () => {
            setIsMobile(window.innerWidth <= 1000);
        };

        window.addEventListener("resize", handleResize);
        handleResize();

        return () => {
            window.removeEventListener("resize", handleResize);
        };
    }, []);

    const toggleFilter = (key: string) => {
        setFilters((prevFilters) => ({
            ...prevFilters,
            [key]: !prevFilters[key],
        }));
    };

    const filteredAchievements = achievements.filter((achievement: any) => {
        if (!filters.showEarned && achievement.dateEarned !== null) return false;
        if (!filters.showNotEarned && achievement.dateEarned === null) return false;
        if (!filters.showHidden && achievement.hidden === true) return false;
        if (!filters.showNonHidden && achievement.hidden === false) return false;
        if (!filters.showAmount && achievement.category === "Amount of Predictions") return false;
        if (!filters.showTiming && achievement.category === "Timing and Strategy") return false;
        if (!filters.showWeekly && achievement.category === "Weekly and Cumulative Performance") return false;
        if (!filters.showStreaks && achievement.category === "Streaks and Trends") return false;
        if (!filters.showSpecial && achievement.category === "Special Predictions") return false;
        if (!filters.showMocking && achievement.category === "Mocking Achievements") return false;
        if (searchQuery
            && !achievement.name.toLowerCase().includes(searchQuery.toLowerCase())
            && !achievement.description.toLowerCase().includes(searchQuery.toLowerCase())
        ) return false;

        // Add additional filtering logic based on categories as needed
        return true;
    });

    return (
        <LoggedInLayout>
            <div className="container mx-auto p-4 text-white">
                <h1 className="text-3xl font-bold text-center">Achievements of {username}</h1>
                <AchievementEarnedOverview />
                <AchievementFilters
                    filters={filters}
                    toggleFilter={toggleFilter}
                    searchQuery={searchQuery}
                    setSearchQuery={setSearchQuery} />
                <div className="text-center flex flex-col items-center gap-2">
                    {filteredAchievements.map((achievement: any) => (
                        isMobile ?
                            <AchievementCard key={achievement.id} achievement={achievement} isMobile={isMobile} />
                            :
                            <AchievementRow key={achievement.id} achievement={achievement} />
                    ))}
                </div>
            </div>
        </LoggedInLayout>
    );
}

export default AchievementsPage;
