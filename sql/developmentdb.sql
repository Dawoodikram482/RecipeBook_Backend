-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: mysql
-- Generation Time: Jun 30, 2024 at 03:41 PM
-- Server version: 11.1.2-MariaDB-1:11.1.2+maria~ubu2204
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `developmentdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `Recipe`
--

CREATE TABLE `Recipe` (
  `RecipeId` int(11) NOT NULL,
  `RecipeTitle` varchar(200) NOT NULL,
  `Ingredients` varchar(2000) NOT NULL,
  `Instructions` varchar(2000) NOT NULL,
  `Image` varchar(500) NOT NULL,
  `Category` enum('Breakfast','Lunch','Dinner') NOT NULL,
  `Createdate` datetime NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Recipe`
--

INSERT INTO `Recipe` (`RecipeId`, `RecipeTitle`, `Ingredients`, `Instructions`, `Image`, `Category`, `Createdate`, `user_id`) VALUES
(1, '      Egg and Sausage Sandwiches', '4 tsp. olive oil, divided\n2 thin slices red onion\n12 oz. sweet Italian sausage (casings removed)\n2 oz. extra-sharp Cheddar cheese, coarsely grated\n4 large eggs\n4 English muffins, split and toasted\n6 sweet piquante peppers, sliced (we used Peppadews)\n1/4 c. flat-leaf parsley', 'Step 1\nHeat 2 tsp oil in large cast-iron skillet on medium. Add onion and cook 3 minutes. With wet hands, shape sausage into four 1/4-inch-thick patties, add to skillet with onion, and increase heat to medium-high. Flip onion and cook until just tender, 2 to 3 minutes more. Cook patties until browned, 2 to 3 minutes, then flip.\nStep 2\nSeparate onion slices into rings and arrange on top of patties, then top with Cheddar and continue cooking until sausage is cooked through, 2 to 3 minutes more.\nStep 3\nMeanwhile, heat remaining 2 teaspoons olive oil in large nonstick skillet on medium and cook eggs to desired doneness, 4 to 5 minutes for runny yolks. Top bottom halves of muffins with sausage, then eggs, pepper slices, and parsley', 'sausage-and-egg-sandwiches.jpg', 'Breakfast', '2024-01-17 00:00:00', 1),
(2, '      French Toast', '6 large eggs\n1 1/2 c. whole milk \n1 1/2 tsp. ground cinnamon\n1 1/2 tsp. pure vanilla extract\n8 1-inch-thick slices challah bread\n4 tbsp. unsalted butter\nYogurt, berries, and pure maple syrup or honey, for serving', 'Step 1\nIn large, shallow bowl, whisk together eggs, milk, cinnamon, and vanilla.  \nStep 2\nWorking in batches, place 2 bread slices in egg mixture and let soak 2 minutes. Flip and soak 1 minute more (both sides of bread should be totally coated in mixture). \nStep 3\nMeanwhile, heat 1 tablespoon butter in large nonstick skillet on medium-low. Once melted, add soaked bread and cook until golden brown, 1 to 3 minutes per side; transfer to wire rack. While toast is cooking, soak next batch of challah slices. \nStep 4\nRepeat with remaining butter and bread. Serve topped with yogurt, berries, and syrup or honey if desired.', 'how-to-make-french-toast.jpg', 'Breakfast', '2024-01-17 00:00:00', 1),
(3, '  Cheesy Scalloped Potatoes ', '1 tsp butter\n2 cloves garlic\n1 tsp flour\n1 cup milk(240 mL)\n1 tsp salt\n1/2 tsp pepper\n3 yukon potatoes, peeled\n2 tsp grated parmesan cheese\nfresh parsley, chopped, for garnish', 'Step 1\nPreheat the oven to 350°F (180°C). In a small pot, melt butter, fry garlic until starting to brown, add flour, salt, and pepper. Whisk until smooth. Drizzle in milk, whisking constantly. Bring to a boil, then remove from heat.\nStep 2\nSlice potatoes into ⅛-inch (3 mm) thick slices and fan out sliced potatoes in a small baking dish.\nStep 3\nPour the prepared sauce over the potatoes.\nSprinkle with Parmesan. Bake for 1 hour until bubbly and golden brown. Sprinkle chopped parsley on top before serving.&amp;amp;gt;&amp;gt;', 'Cheesy-Scalloped-Potatoes.jpg', 'Lunch', '2024-01-17 00:00:00', 2),
(4, 'Spicy Chicken Mac and Cheese', '1 box Macaroni and Cheese Box \r\n5 tspunsalted butter, divided\r\n1/4 cup milk(60 mL)\r\n1/2 medium red onion, diced\r\n1/2 tspkosher salt\r\n1/4 tsp cayenne pepper\r\n1 cup shredded cooked chicken(125 g)\r\n1 tspKraft® Ranch Dressing\r\n1 tsp Hot Sauce\r\n1 green onion, sliced on the bias', 'Step 1\r\nBring 6 cups (1.5 l) of water to a boil in a medium saucepan over medium-high heat. Stir in the macaroni and cook for 7–8 minutes, or until tender, stirring occasionally.\r\nStep 2\r\nDrain the macaroni in a colander (do not rinse), then return to the pot over low heat. Add 4 tablespoons of butter, the milk, and cheese sauce mix. Stir well until creamy. Remove the pot from the heat.\r\nStep 3 \r\nMelt the remaining tablespoon of butter in a medium skillet over medium heat. Add red onion, salt, cayenne, and shredded chicken. Sauté for 1–2 minutes until the onion is tender, and the chicken is heated through. Mix the chicken mixture into the mac and cheese. Transfer it to a serving bowl, drizzle Ranch Dressing and Hot Sauce on top, and garnish with sliced green onion.', 'Buffalo-Mac-Cheese-Reshoot.jpg', 'Lunch', '2024-01-17 00:00:00', 2),
(5, ' Paprika Chicken and Rice Bake', '5 chicken thighs\n1 tspsalt, plus more to taste\n1 tsppepper, plus more to taste\n1 tsppaprika\n1 tspdried parsley\n1 tspolive oil\n1 tsp garlic, minced\n1/2 cup red onion(75 g), diced\n1 cup long grain rice(200 g)\n1 1/2 cups chicken broth(360 mL)', 'Step 1\nPreheat the oven to 400˚F (200˚C). In a large bowl, season chicken thighs with salt, pepper, paprika, and parsley.\nStep 2\nHeat olive oil in an oven-proof pot. Sear chicken thighs, skin-side down, until the skin is crispy (5-6 minutes each side). Remove chicken. Then, in the pot, add garlic and onions; cook until onions are transparent. Pour in rice and chicken broth, season with salt and pepper. Stir and bring to a boil.\nStep 3\nPlace chicken thighs back in the pot, skin-side up, on top of the rice. Bring to a boil, cover with a lid, and bake for 35-40 minutes until rice is fully cooked.\nOptionally, for crispy skin, remove chicken thighs and broil.&amp;gt;', 'paprikachicken.jpg', 'Dinner', '2024-01-17 00:00:00', 2),
(6, 'Honey Garlic Chicken', '6 bone-in, chicken thighs\r\nsalt, to taste\r\npepper, to taste\r\n1 tspunsalted butter\r\n3 cloves garlic\r\n1 tsp brown sugar\r\n1/4 cup honey(85 g)\r\n1 tsp dried thyme\r\n1 tps dried oregano\r\n1 lb green beans(450 g)', 'Step 1\r\nPreheat the oven to 400˚F (200˚C) and season chicken thighs with salt and pepper.\r\nStep 2\r\nMelt 1 tablespoon butter in a large ovenproof skillet over medium heat. Sear chicken thighs, skin-side down, until golden brown on both sides. Remove and set aside. Pour out excess fat, leaving some for the sauce. Add garlic to the skillet; stir until fragrant. Add brown sugar, honey, thyme, and oregano and stir to combine. Reduce heat to low.\r\nStep 3\r\nReturn chicken to the skillet, coating it in the sauce. Add green beans to the skillet. Bake for 25 minutes or until the chicken is cooked through.', 'Honey-Garlic-Chicken.jpg', 'Dinner', '2024-01-17 00:00:00', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` text NOT NULL,
  `role` enum('Visitor','Admin') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `role`) VALUES
(1, 'Parranasian', '$2y$10$Gqtgn2UvLUBxdOfjy2EpoOkIJMOJU34G5I2cotXMe5vry83stBGyC', 'parranasian@gmail.com', 'Admin'),
(2, 'Dawood', '$2y$10$K3ufF76cBguhWe5dbnxZKel2r8E/xB2XTtT8FnvQRveRM8oQycOpW', 'dawood@gmail.com', 'Admin'),
(5, 'Areesha', '$2y$10$VCuPTcMSS9PoXQeRv8ktZ.d1ejnG61WdjTdg1r2nV8APx7s4fnVay', 'areesha@bongimail.com', 'Admin'),
(7, 'Ikram', '$2y$10$TmD5M0h/MjMM2SWQ0VetluxXv7xlY4hMiDN4UqcuWqT4Uz7PrVGXK', 'Ikram@gmail.com', 'Visitor');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Recipe`
--
ALTER TABLE `Recipe`
  ADD PRIMARY KEY (`RecipeId`),
  ADD KEY `recipe_creator_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Recipe`
--
ALTER TABLE `Recipe`
  MODIFY `RecipeId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Recipe`
--
ALTER TABLE `Recipe`
  ADD CONSTRAINT `recipe_creator_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
