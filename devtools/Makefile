#[ DEVTOOLS

PROJECT_NAME=
GITHUB_ORIGIN=master

#]

#[ PROJET GLOBAL UTILITIES

# print the project init steps and requirements
project-print-init:
	@echo 1. Define the required variable in the devtools makefile
	@echo 2. Define the extra elements to ignore in the .gitignore file
	@echo 3. Create the linked GitHub Project
	@echo 4. Fill the missing elements in the project readme
	@echo 5. Add your custom commands in the makefile
	@echo 6. Launch the 'project-init' command
	@echi 7. Go to configure the project branches protection rules

# create the project initialization branches
project-create-init-branches:
# dev branch
	git branch dev
	git switch dev
	git push --set-upstream origin dev
	git branch features/dev/init
	git switch features/dev/init
	git push --set-upstream origin features/dev/init
	git switch $(GITHUB_ORIGIN)
# documentation branch
	git branch documentation
	git switch documentation
	git push --set-upstream origin documentation
	git branch features/documentation/init
	git switch features/documentation/init
	git push --set-upstream origin features/documentation/init
	git switch $(GITHUB_ORIGIN)

# init the project
project-init:	
	@echo Create initialization branches
	@make project-create-init-branches
	@echo Everything is ok
	
#]