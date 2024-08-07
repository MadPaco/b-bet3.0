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

    const loadAchievements = async () => {
        try {
            const [allData, hiddenData] = await Promise.all([
                fetchAllAchievements(username),
                fetchHiddenAchievements(username),
            ]);

            setAchievements([...allData, ...hiddenData]);
        } catch (error) {
            console.error('Error fetching achievements:', error);
        }
    };

    useEffect(() => {

        loadAchievements();

        const handleResize = () => {
            setIsMobile(window.innerWidth <= 1000);
        };

        window.addEventListener("resize", handleResize);
        handleResize();

        return () => {
            window.removeEventListener("resize", handleResize);
        };
    }, [username]);

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
            <div className="container mx-auto p-4 text-white text-center">
                <h1 className='my-3 text-highlightGold text-xl font-bold text-shadow-sm shadow-black'>Achievements of {username}</h1>
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
