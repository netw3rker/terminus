<?php

namespace Pantheon\Terminus\UnitTests\Commands\Org\Team;

use Pantheon\Terminus\Commands\Org\Team\AddCommand;
use Pantheon\Terminus\Models\Workflow;

/**
 * Testing class for Pantheon\Terminus\Commands\Org\Team\AddCommand
 */
class AddCommandTest extends OrgTeamCommandTest
{
    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();

        $this->command = new AddCommand($this->getConfig());
        $this->command->setLogger($this->logger);
        $this->command->setSession($this->session);
    }

    /**
     * Tests the org:team:add command
     */
    public function testAdd()
    {
        $email = 'devuser@pantheon.io';
        $role = 'user_role';
        $org_name = 'org_name';
        $workflow = $this->getMockBuilder(Workflow::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->org_user_memberships->expects($this->once())
            ->method('create')
            ->with()
            ->willReturn($workflow);
        $workflow->expects($this->once())
            ->method('checkProgress')
            ->willReturn(true);
        $this->organization->expects($this->once())
            ->method('get')
            ->with($this->equalTo('profile'))
            ->willReturn((object)['name' => $org_name,]);

        $this->logger->expects($this->once())
            ->method('log')
            ->with(
                $this->equalTo('notice'),
                $this->equalTo('{email} has been added to the {org} organization as a(n) {role}.'),
                $this->equalTo(['email' => $email, 'org' => $org_name, 'role' => $role,])
            );

        $out = $this->command->add($this->organization->id, $email, $role);
        $this->assertNull($out);
    }
}
