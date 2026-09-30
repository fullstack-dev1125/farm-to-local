# "What kind?" taxonomy — tag groups per category (Sep 29)

Every category has a tag group. A seller who checks a category sees that group on listing step 2 ("What kind?"),
picks all that fit, and can type a new one. Typed tags save as `status=pending`, show on that seller's page with a
"pending" pill, and staff approve / rename / merge within a day; then they are global. Tags render as pills on cards
and shop pages, filter in results (the "What kind" menu, designs/city-austin.html, grouped by category with a search
box), drive the trade tabs on the Services directory, and get a city sub-page `/{state}/{city}/{category}/{tag}` when
≥ 5 sellers match. "Suggest a category" (top level) is a form to staff, not self-serve — categories are the 12 fixed ones.

Seed these exactly (slug = kebab-case of the name; `group` = category slug):

## Produce (`produce`) — 14
Vegetables, Fruit, Berries, Melons, Citrus, Stone fruit, Mushrooms, Microgreens & sprouts, Fresh herbs, Cut flowers, Pecans & nuts, Pumpkins & gourds, U-pick, CSA box

## Meat & Poultry (`meat`) — 12
Beef, Pork, Lamb & goat, Chicken, Turkey, Duck & game birds, Rabbit, Bison & elk, Bulk · quarter / half / whole, Bone broth & organs, Sausage & jerky, Raw pet food

## Eggs & Dairy (`eggs`) — 9
Chicken eggs, Duck & quail eggs, Raw milk / herd share, A2 milk, Goat milk, Cheese, Butter & cream, Yogurt & kefir, Ice cream

## Seafood (`seafood`) — 7
Shrimp, Oysters & clams, Crab & crawfish, Finfish, Smoked & cured, CSF share, Bait & live

## Bakery & Sweets (`bakery`) — 16
Sourdough, Bread & rolls, Bagels, Tortillas, Pastries & croissants, Cakes & custom cakes, Pies & cobblers, Cookies & bars, Cupcakes, Donuts, Gluten-free baking, Vegan baking, Empanadas & hand pies, Tamales, Candy & chocolate, Kolaches

## Pantry & Preserves (`pantry`) — 13
Honey & bee products, Jams & jellies, Pickles & ferments, Hot sauce & salsa, Sauces & dressings, Dried herbs & spices, Flour & grains, Nut butters, Coffee & tea, Maple syrup & sorghum, Tallow & lard, Kombucha & drinks, Granola & snacks

## Wellness & Body (`wellness`) — 12
Soap, Tallow balm & salves, Lotion & body butter, Lip balm, Tinctures & elderberry, Herbal teas, Candles, Deodorant, Beard & hair care, Bath bombs & scrubs, Bug spray, Pet care

## Plants & Starts (`plants`) — 11
Vegetable starts, Fruit trees, Berry plants, Native plants, Herb plants, Flowers & bulbs, Seeds, Houseplants, Trees & shrubs, Cuttings & rootstock, Mushroom spawn

## Prepared Food & Meals (`prepared`) — 21
Mexican, Tex-Mex, Italian, BBQ & smoked, Southern & soul food, Cajun & Creole, Thai, Vietnamese, Chinese, Japanese, Korean, Indian, Filipino, Mediterranean & Middle Eastern, Ethiopian, Caribbean, Vegan & plant-based, Gluten-free, Keto & low-carb, Meal prep & weekly meals, Catering & events

## Homestead Goods (`homestead`) — 13
Chicken coops & tractors, Raised beds, Fencing & gates, Hand tools, Cutting boards & woodwork, Pottery & ceramics, Baskets & textiles, Leather goods, Beeswax wraps, Furniture, Smokers & grills, Kits & plans, Quilts & knits

## Farm & Garden Services (`services`) — 22
Mobile butchering / on-farm harvest, Cut & wrap / processing, Chicken coop & tractor builds, Fence & gate building, Home garden consulting, Raised-bed & garden installation, Beekeeping & swarm removal, Pruning & orchard care, Tree removal & milling, Livestock hauling, Farm sitting & animal care, Classes & workshops, Soil testing & compost delivery, Irrigation & drip, Greenhouse & high-tunnel builds, Tractor & brush work, Farrier & hoof trimming, Shearing, Custom hay & baling, Well, pump & water, Large-animal vet, Marketing, photos & web

## Rules
- A pending tag never appears in filters or on other sellers' pages.
- Merging keeps the seller's pill (it re-points to the surviving tag).
- Tags are per category: "Sourdough" lives in bakery; a Pantry seller who types "sourdough" gets offered the bakery tag, not a duplicate.
- Practice/certification tags (Grass-fed, Certified organic, Wild-caught…) are a separate group and unchanged.
- The Services group doubles as the trade list on `/services/{state}/{city}` (designs/services-austin.html) — 22 trades + "Suggest a trade".