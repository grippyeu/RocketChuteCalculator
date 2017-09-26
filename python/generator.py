 # $surface = (2 * GRAVITATIONAL_CONSTANT * $mass) / (AIR_DENSITY * $cd * pow($descentRate, 2));
 #  $diameter = sqrt(4 * $surface / pi());

import math
 
# This program generator a file containing descent rate and weight based on a CD of .75.

# constants
GRAVITATIONAL_CONSTANT = 9.81
AIR_DENSITY = 1225
CD = 0.75

# variables

# mass in grams 
rocketMass = 10

# descent rate in m / s 
rocketDescentRate = 3.5
rocketDescentRate2 = 4.0
rocketDescentRate3 = 4.5
rocketDescentRate4 = 5.0
rocketDescentRate5 = 5.5
rocketDescentRate6 = 6.0

# file 
fo = open("data.txt", "wb")
#fo.write("Generated file for a descent rate of " + str(rocketDescentRate) + "m/s \t \n")
fo.write("M\t D3.5 \t D4.0 \t D4.5 \t D5.0 \t D5.5 \t D6.0 \n")

# loop
for rocketMass in range(10,3005,5): 
	print ("Mass", rocketMass)

	surface = (2 * GRAVITATIONAL_CONSTANT * rocketMass) / (AIR_DENSITY * CD * pow(rocketDescentRate, 2))
	surface2 = (2 * GRAVITATIONAL_CONSTANT * rocketMass) / (AIR_DENSITY * CD * pow(rocketDescentRate2, 2))
	surface3 = (2 * GRAVITATIONAL_CONSTANT * rocketMass) / (AIR_DENSITY * CD * pow(rocketDescentRate3, 2))
	surface4 = (2 * GRAVITATIONAL_CONSTANT * rocketMass) / (AIR_DENSITY * CD * pow(rocketDescentRate4, 2))
	surface5 = (2 * GRAVITATIONAL_CONSTANT * rocketMass) / (AIR_DENSITY * CD * pow(rocketDescentRate5, 2))
	surface6 = (2 * GRAVITATIONAL_CONSTANT * rocketMass) / (AIR_DENSITY * CD * pow(rocketDescentRate6, 2))
	print("Surface (debug)", surface)
	
	diameter = math.sqrt(4 * surface / math.pi)
	diameter2 = math.sqrt(4 * surface2 / math.pi)
	diameter3 = math.sqrt(4 * surface3 / math.pi)
	diameter4 = math.sqrt(4 * surface4 / math.pi)
	diameter5 = math.sqrt(4 * surface5 / math.pi)
	diameter6 = math.sqrt(4 * surface6 / math.pi)
	print("Diameter", diameter)

	line = str(rocketMass) + "\t %0.2f" %diameter + "\t %0.2f" %diameter2 + "\t %0.2f" %diameter3 + "\t %0.2f" %diameter4 + "\t %0.2f" %diameter5 + "\t %0.2f" %diameter6 + "\t \n"
	
	fo.write(line) 

# close file
fo.close()


 
