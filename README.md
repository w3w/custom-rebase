==Usage==

The script takes two params - `from` and `to`. Those specify the treeishs of 
bottom and tip of the branch that is being rebased. Then create a branch 
where you want to rebase to. And then start the script. 

So for example if you want to rebase branch my-project from tag-1 to tag-2, 
you would create backup branch backup/my-project at my-project. Then hard reset
my-project to tag-2. Then run the script with params: `tag-1 backup/my-project`

Please note that the directory of the script needs to be writable (it writes 
commit message file to it). 
