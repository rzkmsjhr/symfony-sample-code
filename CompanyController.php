<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Company;

/**
 * @Route("/api", name="api_")
 */
class CompanyController extends AbstractController
{
    /**
     * @Route("/company", name="app_company", methods={"GET"})
     */
    public function index(ManagerRegistry $doctrine): Response
    {
        $companies = $doctrine
            ->getRepository(Company::class)
            ->findAll();

        $data = [];

        foreach ($companies as $company) {
            $data[] = [
                'id'       => $company->getId(),
                'name'     => $company->getName(),
                'email'    => $company->getEmail(),
                'phone'    => $company->getPhone(),
                'address'  => $company->getAddress(),
                'website'  => $company->getWebsite(),
                'industry' => $company->getIndustry(),
            ];
        }

        return $this->json($data);
    }

    /**
     * @Route("/company", name="add_company", methods={"POST"})
     */
    public function addCompany(ManagerRegistry $doctrine, Request $request): Response
    {
        $entityManager = $doctrine->getManager();

        $company = new Company();
        $company->setName($request->request->get('name'));
        $company->setEmail($request->request->get('email'));
        $company->setPhone($request->request->get('phone'));
        $company->setAddress($request->request->get('address'));
        $company->setWebsite($request->request->get('website'));
        $company->setIndustry($request->request->get('industry'));

        $entityManager->persist($company);
        $entityManager->flush();

        return $this->json('New Company has been added successfully with id ' . $company->getId());
    }

    /**
     * @Route("/company/{id}", name="company_show", methods={"GET"})
     */
    public function showCompany(ManagerRegistry $doctrine, int $id): Response
    {
        $company = $doctrine->getRepository(Company::class)->find($id);

        if (!$company) {
            return $this->json('No Company found for id ' . $id, 404);
        }

        $data = [
            'id'       => $company->getId(),
            'name'     => $company->getName(),
            'email'    => $company->getEmail(),
            'phone'    => $company->getPhone(),
            'address'  => $company->getAddress(),
            'website'  => $company->getWebsite(),
            'industry' => $company->getIndustry(),
        ];

        return $this->json($data);
    }

    /**
     * @Route("/company/{id}", name="company_edit", methods={"PUT", "PATCH"})
     */
    public function editCompany(ManagerRegistry $doctrine, Request $request, int $id): Response
    {
        $entityManager = $doctrine->getManager();
        $company = $entityManager->getRepository(Company::class)->find($id);

        if (!$company) {
            return $this->json('No Company found for id ' . $id, 404);
        }

        $content = json_decode($request->getContent(), true);

        $company->setName($content['name']);
        $company->setEmail($content['email']);
        $company->setPhone($content['phone']);
        $company->setAddress($content['address']);
        $company->setWebsite($content['website']);
        $company->setIndustry($content['industry']);

        $entityManager->flush();

        $data = [
            'id'       => $company->getId(),
            'name'     => $company->getName(),
            'email'    => $company->getEmail(),
            'phone'    => $company->getPhone(),
            'address'  => $company->getAddress(),
            'website'  => $company->getWebsite(),
            'industry' => $company->getIndustry(),
        ];

        return $this->json($data);
    }

    /**
     * @Route("/company/{id}", name="company_delete", methods={"DELETE"})
     */
    public function deleteCompany(ManagerRegistry $doctrine, int $id): Response
    {
        $entityManager = $doctrine->getManager();
        $company = $entityManager->getRepository(Company::class)->find($id);

        if (!$company) {
            return $this->json('No Company found for id ' . $id, 404);
        }

        $entityManager->remove($company);
        $entityManager->flush();

        return $this->json('Deleted a Company successfully with id ' . $id);
    }
}
